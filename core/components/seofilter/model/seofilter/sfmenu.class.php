<?php

class sfMenu
{
    /** @var modX $modx */
    public $modx;
    /** @var  pdoFetch|pdoTools $pdoTools */
    public $pdoTools;
    /** @var array $config */
    public $config = array();
    /** @var array $tree */
    protected $tree = array();
    /** @var array $parents */
    protected $parents = array();
    /** @var int $level */
    protected $level = 1;
    /** @var int $idx */
    protected $idx = 1;

    public function __construct(modX & $modx, $config = array())
    {
        $this->modx = &$modx;

        $config = array_merge(
            array(
                'firstClass' => 'first',
                'lastClass' => 'last',
                'hereClass' => 'active',
                'parentClass' => '',
                'rowClass' => '',
                'outerClass' => '',
                'innerClass' => '',
                'levelClass' => '',
                'selfClass' => '',
                'webLinkClass' => '',
                'limit' => 0,
            ),
            $config,
            array(
                'return' => 'data',
            )
        );

        if (empty($config['tplInner']) && !empty($config['tplOuter'])) {
            $config['tplInner'] = $config['tplOuter'];
        }

        if(empty($config['context'])){
            $config['context'] = $modx->context->key;
        }

        if (empty($config['hereId']) && !empty($modx->getPlaceholder('sf.seo_id'))) {
            $config['hereId'] = (int)$modx->getPlaceholder('sf.seo_id');
        }

        $this->config = $config;

        $fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
        $path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
        if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
            $this->pdoTools = new $pdoClass($modx, $config);
        } else {
            return;
        }

//        if (isset($config['hereId'])) {
//            $time = microtime(true);
//            $tmp = $this->urlParents($config['hereId']);
//            $tmp[] = $config['hereId'];
//            $this->parents = $tmp;
//            $this->pdoTools->addTime('Count parents for Current Link complete', microtime(true) - $time);
//        }

        $modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
        $modx->lexicon->load('seofilter:default');
    }

    /**
     * DEPRECATED METHOD
     * The new findParents */
    public function urlParents($id = 0) {
        $parents = array();
        $words = array();
        $rule_id = 0;
        $page_id = 0;

        $q = $this->modx->newQuery('sfUrls');
        $q->leftJoin('sfUrlWord','sfUrlWord','sfUrlWord.url_id = sfUrls.id');
        $q->where(array('sfUrls.id'=>$id));
        $q->sortby('sfUrlWord.priority','ASC');
        $q->select('sfUrls.id,sfUrls.multi_id,sfUrls.page_id,sfUrlWord.field_id,sfUrlWord.word_id,sfUrlWord.priority');
        if($q->prepare() && $q->stmt->execute()) {
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $rule_id = $row['multi_id'];
                $page_id = $row['page_id'];
                $words[] = $row;
            }
        }
        if(count($words) > 1) {
            $q = $this->modx->newQuery('sfUrls');
            $q->where(array(
                'sfUrls.id:!='=>$id,
                'sfUrls.multi_id:!='=>$rule_id,
                'sfUrls.page_id'=>$page_id,
            ));
            if(!(int)$this->config['double']) {
                 $q->limit(1);
            }
            if($this->config['sortby'] && $this->config['sortdir']) {
                $q->sortby($this->config['sortby'], $this->config['sortdir']);
            }
            $select = array('sfUrls.id,sfUrls.link,sfUrls.page_id,sfUrls.multi_id');
            foreach($words as $key => $word) {
                if($key < count($words) - 1) {
                    $q->rightJoin('sfUrlWord','sfUrlWord'.$key,'sfUrlWord'.$key.'.url_id = sfUrls.id');
                    $q->where(array(
                        'sfUrlWord'.$key.'.field_id'=>$word['field_id'],
                        'sfUrlWord'.$key.'.word_id'=>$word['word_id'],
                    ));
                }
            }
            $q->select($select);
            $q->groupby('sfUrls.id');
            if($q->prepare() && $q->stmt->execute()) {
                while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($this->modx->getCount('sfUrlWord',array('url_id'=>$row['id'])) == count($words) - 1) {
                        $parents[] = $row['id'];
                    }
                }
            }
            foreach($parents as $parent) {
                $parents = array_merge($parents,$this->urlParents($parent));
            }
        }

        return array_unique($parents);
    }

    public function getTree($rules = '',$parents = '') {
        $tree = array();
        $time = microtime(true);
        $this->pdoTools->addTime('Tree start built');
        if(!$rules) {
            $rules = $this->config['rules'];
        }
        if(!$parents) {
            $parents = $this->config['parents'];
        }
        if ($pre_array = $this->rulesArray($rules,$parents)) {
            $tree = $this->getLinks($pre_array);

            $tree = $this->prepareLinks($tree);

            if((int)$this->config['countChildren']) {
                $tree = $this->countChildren($tree);
            }

            if((int)$this->config['nesting'] && !(int)$this->config['groupbyrule']) {
                $tree = $this->linksNesting($tree);
            }

            if((int)$this->config['groupbyrule']) {
                $tree = $this->groupByRule($tree,$pre_array);
            }
        }
        $this->pdoTools->addTime('Tree was built (AllTime)', microtime(true) - $time);
        return $tree;
    }

    public function rulesArray($rules = '',$parents = '') {
        $time = microtime(true);
        $pre_array = array();
        $rules_in = $rules_out = array();
        $parents_in = $parents_out = array();
        $system = array('field_id','multi_id','priority','class','key','alias','slider','exact');

        if($parents) {
            $parents = array_map('trim', explode(',', $parents));
            foreach ($parents as $v) {
                if (!is_numeric($v)) {
                    continue;
                }
                if ($v[0] == '-') {
                    $parents_out[] = abs($v);
                } else {
                    $parents_in[] = abs($v);
                }
            }
        }
        if($rules) {
            $rules = array_map('trim', explode(',', $rules));
            foreach ($rules as $v) {
                if (!is_numeric($v)) {
                    continue;
                }
                if ($v[0] == '-') {
                    $rules_out[] = abs($v);
                } else {
                    $rules_in[] = abs($v);
                }
            }
        }

        $q = $this->modx->newQuery('sfRule');
        $qwhere = array();
        if(count($rules_in)) {
            $qwhere['id:IN'] = $rules_in;
        }
        if(count($rules_out)) {
            $qwhere['id:NOT IN'] = $rules_out;
        }
        if(count($parents_in)){
            if(count($rules_out) || count($parents_out)) {
                $qwhere['page:IN'] = $parents_in;
            } else {
                $qwhere['OR:page:IN'] = $parents_in;
            }
        }
        if(count($parents_out)) {
            $qwhere['page:NOT IN'] = $parents_out;
        }
        $qwhere['active'] = 1;
        $q->where($qwhere);
        $q->leftJoin('sfFieldIds','sfFieldIds','sfFieldIds.multi_id = sfRule.id');
        $q->innerJoin('sfField','sfField','sfField.id = sfFieldIds.field_id');
        $q->select(array(
            'sfRule.*',
            'sfFieldIds.field_id,sfFieldIds.multi_id,sfFieldIds.priority',
            'sfField.class,sfField.key,sfField.alias,sfField.slider,sfField.exact',
        ));
        $q->sortby('sfFieldIds.priority','ASC');
        if($q->prepare() && $q->stmt->execute()) {
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $field = array_intersect_key($row,array_flip($system));
                $field_id = $row['field_id'];
                $row = array_diff_key($row,array_flip($system));
                if(isset($pre_array[$row['id']])) {
                    $pre_array[$row['id']] = array_merge($pre_array[$row['id']],$row);
                } else {
                    $pre_array[$row['id']] = $row;
                }
                $pre_array[$row['id']]['fields'][$field_id] = $field;
            }
        }
        $this->pdoTools->addTime('Rules array complete ', microtime(true) - $time);
        return $pre_array;
    }

    public function getLinks($pre_array) {
        $time = microtime(true);
        $links = array();
        $q = $this->modx->newQuery('sfUrls');
        $where = array('active'=>1,'multi_id:IN'=>array_keys($pre_array));
        if(!(int)$this->config['showHidden']) {
            $where['menu_on']=1;
        }
        $q->where($where);
        $q->leftJoin('sfUrlWord','sfUrlWord','sfUrlWord.url_id = sfUrls.id');
        $q->innerJoin('sfDictionary','sfDictionary','sfDictionary.id = sfUrlWord.word_id');
        $q->select(array(
            'sfUrls.*',
            'sfUrlWord.word_id,sfUrlWord.field_id',
            'sfDictionary.alias,sfDictionary.input,sfDictionary.value'
        ));
        if($this->config['sortby'] && $this->config['sortdir']) {
            $q->sortby($this->config['sortby'], $this->config['sortdir']);
        }
        $q->limit((int)$this->config['limit'],(int)$this->config['offset']);
        if($q->prepare() && $q->stmt->execute()) {
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $url = $row['new_url']?:$row['old_url'];
                $url = $this->modx->makeUrl($row['page_id'],$this->config['context'],'',$this->config['scheme']).$url;
                $name = $row['menutitle']?:$row['link'];
                $row['url'] = $url;
                $row['name'] = $name;
                $row['rule_name'] = $pre_array[$row['multi_id']]['name'];
                $row['rule_id'] = $row['multi_id'];
                $row['rank'] = $pre_array[$row['multi_id']]['rank'];
                $row['count_where'] = $pre_array[$row['multi_id']]['count_where'];
                $row['count_parents'] = $pre_array[$row['multi_id']]['count_parents'];
                $word_id = $row['word_id'];
                $word_array = array(
                    'multi_id'=>$row['multi_id'],
                    'field_id'=>$row['field_id'],
                    'class'=>$pre_array[$row['multi_id']]['fields'][$row['field_id']]['class'],
                    'key'=>$pre_array[$row['multi_id']]['fields'][$row['field_id']]['key'],
                    'field_alias'=>$pre_array[$row['multi_id']]['fields'][$row['field_id']]['alias'],
                    'priority'=>$pre_array[$row['multi_id']]['fields'][$row['field_id']]['priority'],
                    'slider'=>$pre_array[$row['multi_id']]['fields'][$row['field_id']]['slider'],
                    'exact'=>$pre_array[$row['multi_id']]['fields'][$row['field_id']]['exact'],
                    'word_id'=>$word_id,
                    'word_input'=>$row['input'],
                    'word_alias'=>$row['alias'],
                    'word_value'=>$row['value'],
                );
                unset($row['word_id']);
                unset($row['field_id']);
                unset($row['input']);
                unset($row['alias']);
                unset($row['value']);
                if(isset($links[$row['id']])) {
                    $links[$row['id']] = array_merge($links[$row['id']],$row);
                } else {
                    $links[$row['id']] = $row;
                }
                $links[$row['id']]['words'][] = $word_array;
            }
        }
        $this->pdoTools->addTime('Get Links complete', microtime(true) - $time);
        return $links;
    }

    public function prepareLinks($links = array()) {
        $tree = array();
        $time = microtime(true);

        foreach($links as $key => $link) {
            if(isset($link['words'])) {
                $words = $link['words'];
                $link['level'] = count($words);
                usort($words, function ($a, $b) {
                    if ($a['priority'] == $b['priority']) {
                        return 0;
                    } else {
                        return ($a['priority'] < $b['priority']) ? -1 : 1;
                    }
                });
                $link['words'] = $words;
            } else {
                $link['level'] = 0;
            }

            if($maxlevel = (int)$this->config['level']) {
                if($link['level'] <= $maxlevel) {
                    $tree[$key] = $link;
                }
            } else {
                $tree[$key] = $link;
            }
        }


        $this->pdoTools->addTime('Sort links complete ', microtime(true) - $time);
        return $tree;
    }

    public function countChildren($links = array()) {
        $tree = array();
        $time = microtime(true);
        foreach ($links as $link) {
            $total = 0;
            $addTVs = $fields_where = $innerJoin = array();
            foreach ($link['words'] as $field) {
                switch ($field['class']) {
                    case 'msProductData':
                    case 'modResource':
                    case 'msProductOption':
                        $fw = $field['class'] . '.' . $field['key'];
                        if ($field['class'] == 'msProductData') {
                            $innerJoin['msProductData'] = array('class' => 'msProductData', 'on' => 'msProductData.id = modResource.id');
                        }
                        if ($field['class'] == 'msProductOption') {
                            $innerJoin['msProductOption'] = array('class' => 'msProductOption', 'on' => 'msProductOption.product_id = modResource.id');
                            $fields_where[$field['class'] . '.key'] = $field['key'];
                            $fw = $field['class'] . '.value';
                        }
                        if ($field['slider']) {
                            $slider = explode(',', $field['word_input']);
                            $fields_where[$fw . ':>='] = $slider[0];
                            if ($slider[1]) {
                                $fields_where[$fw . ':<='] = $slider[1];
                            }
                        } else {
                            $values = explode(',', $field['word_input']);
                            $fields_where[$fw . ':IN'] = $values;
                        }
                        break;
                    case 'modTemplateVar':
                        $addTVs[] = $field['key'];
                        if ($field['exact']) {
                            $fields_where['TV' . $field['key'] . '.value'] = $field['word_input'];
                        } else {
                            $fields_where['TV' . $field['key'] . '.value:LIKE'] = '%' . $field['word_input'] . '%';
                        }
                        break;
                    case 'msVendor':
                        $innerJoin['msProductData'] = array('class' => 'msProductData', 'on' => 'msProductData.id = modResource.id');
                        $innerJoin['msVendor'] = array('class' => 'msVendor', 'on' => 'msVendor.id = msProductData.vendor');
                        $fields_where[$field['class'] . '.id'] = $field['word_input'];
                        break;
                    default:
                        break;
                }
            }

            $addTVs = implode(',', $addTVs);

            if ($link_where = $this->modx->fromJSON($link['count_where'])) {
                $fields_where = array_merge($fields_where,$link_where);
            }
            if ($where = $this->modx->fromJSON($link['count_where'])) {
                $fields_where = array_merge($fields_where,$where);
            }

            if($link['count_parents']) {
                $parents = $link['count_parents'];
            } elseif($link['page_id']) {
                $parents = $link['page_id'];
            } else {
                $parents = $this->config['parents'];
            }

            $this->pdoTools->setConfig(array(
                'showLog' => 0,
                'class' => 'modResource',
                'parents' => $parents,
                'includeTVs' => $addTVs,
                'innerJoin' => $innerJoin,
                'where' => $fields_where,
                'return' => 'data',
                'select' => array(
                    'modResource' => 'COUNT(modResource.id) as count'
                )
            ));

            $run = $this->pdoTools->run();
            if (count($run)) {
                $total = $run[0]['count'];
            }
            $link['total'] = $total;

            if ($mincount = (int)$this->config['mincount']) {
                if ($link['total'] >= $mincount) {
                    $tree[$link['id']] = $link;
                }
            } else {
                $tree[$link['id']] = $link;
            }
        }

        if((int)$this->config['sortcount']) {
            uasort($tree,  function ($a, $b) {
                if ($a['total'] == $b['total']) {return 0;}
                if($this->config['sortdir'] == 'DESC') {
                    return ($a['total'] > $b['total']) ? -1 : 1;
                } else {
                    return ($a['total'] < $b['total']) ? -1 : 1;
                }
            });
        }

        $this->pdoTools->addTime('Count Children complete', microtime(true) - $time);

        return $tree;
    }

    public function recursiveNesting($links = array(),$level = 1) {
        $tree = array();
        $double = (int)$this->config['double'];
        $roots = array();
        $firstlevel = array();

        $levels = $this->recursiveLevels($links);

        foreach($levels as $level => &$linkss) {
            if($level > 1 ) {
                foreach($linkss as $key=> &$link) {
                    if(isset($link['words'])) {
                        $words = $link['words'];
                        $parents = array();
                        $parent_find = 0;
                        foreach ($levels[$level-1] as $pid => &$parent_link) {
                            $find = 0;
                            $childs = array();
                            if(isset($parent_link['words'])) {
                                foreach ($words as $word) {
                                    foreach ($parent_link['words'] as $word_find) {
                                        $f_arr = array_uintersect_assoc($word_find, $word, "strcasecmp");
                                        if (in_array('field_id', array_flip($f_arr)) && in_array('word_id', array_flip($f_arr))) {
                                            $find++;
                                            break;
                                        }
                                    }
                                    if ($find == $level - 1) {
                                        break;
                                    }
                                }
                            }
                            if($find == $level - 1) {
                                $parents[] = $parent_link['id'];
                                $parent_find++;

                                if(isset($parent_link['childrens'])) {
                                    $parent_link['childrens'][] = $link['id'];
                                } else {
                                    $parent_link['childrens'] = array($link['id']);
                                }
                                if(isset($parent_link['inner'])) {
                                    $parent_link['inner'][] = $link;
                                } else {
                                    $parent_link['inner'] = array($link);
                                }

                                if(!$double) {
                                    break;
                                }
                            }
                        }
//                        $link['parents'] = $parents;
                    }
                }
            }
        }

        $tree = $levels[1];

//        $this->modx->log(1,print_r($levels[1],1));

//        foreach($links as $key => $link) {
//            $level = $link['level'];
//            $search = array();
//            $words = $link['words'];
//            $link_find = 0;
//            if($level > 1) {
//                foreach ($links as $s_key => $link_search) {
//                    if ($link_search['level'] != $level - 1) {
//                        continue;
//                    }
//                    $find = 0;
//                    if (isset($link_search['words'])) {
//                        foreach ($words as $word) {
//                            foreach ($link_search['words'] as $word_find) {
//                                $f_arr = array_uintersect_assoc($word_find, $word, "strcasecmp");
//                                if (in_array('field_id', array_flip($f_arr)) && in_array('word_id', array_flip($f_arr))) {
//                                    $find++;
//                                    break;
//                                }
//                            }
//                            if ($find == $level - 1) {
//                                break;
//                            }
//                        }
//                    }
//                    if ($find == $level - 1) {
//                        $search[] = $link_search['id'];
////                        $links[$s_key]['inner'][] = $link;
//                        $link_find++;
//                        if (!$double) {
//                            break;
//                        }
//                    }
//                }
//            } elseif($level) {
//                $firstlevel[] = $link['id'];
//            }
//            if($link_find) {
//                $links[$key]['parents'] = $search;
//            } else {
//                $firstlevel[] = $link['id'];
//            }
//        }
//
//
//
//
//        $firstlevel = array_unique($firstlevel);
//        foreach($firstlevel as $lid) {
//            $tree[$lid] = $links[$lid];
//            unset($links[$lid]);
//        }
//
//
//
//        foreach($links as $key=>$link) {
//            if(isset($link['parents'])) {
//                foreach($link['parents'] as $parent) {
//                    if(isset($links[$parent])) {
//                        $links[$parent]['inner'][] = $link;
//                    }
//                }
//            }
//        }
//        foreach($links as $key=>$link) {
//            if(isset($link['parents'])) {
//                foreach($link['parents'] as $parent) {
//                    if(isset($tree[$parent])) {
//                        $tree[$parent]['inner'][] = $link;
//                    }
//                }
//            }
//        }


        return $tree;
    }

    public function recursiveLevels($links = array()) {
        $levels = array();
        foreach($links as $link) {
            $levels[$link['level']][$link['id']] = $link;
        }
        krsort($levels);
        return $levels;
    }

    public function linksNesting($links = array()) {
        $time = microtime(true);
//        uasort($links,  function ($a, $b) {
//            if ($a['level'] == $b['level']) {return 0;}
//            return ($a['level'] > $b['level']) ? -1 : 1;
//        });

        $tree = $this->recursiveNesting($links);

//        uasort($links,  function ($a, $b) {
//            if ($a['level'] == $b['level']) {return 0;}
//            return ($a['level'] < $b['level']) ? -1 : 1;
//        });

        $tree = $this->multiSort($tree);

        foreach ($tree as $key=>$link) {
            if (isset($link['inner'])) {
                $tree[$key]['childs'] = count($link['inner']);
            } else {
                $tree[$key]['childs'] = 0;
            }
        }

        $this->pdoTools->addTime('Nesting links complete', microtime(true) - $time);
        return $tree;
    }

    public function groupByRule($links = array(),$pre_array = array()) {
        $time = microtime(true);
        $tree = array();
        $groupsort = $this->config['groupsort'];
        $groupdir = $this->config['groupdir'];

        foreach($links as $key=>$link) {
            $pre_array[$link['multi_id']]['links'][] = $link;
            $pre_array[$link['multi_id']]['level'] = count($pre_array[$link['multi_id']]['fields']);
        }

        foreach($pre_array as $key => $pre) {
            if(isset($pre['links'])) {
                $tree[$key] = $pre;
                $tree[$key]['total'] = count($pre['links']);
            }
        }

        if($groupsort) {
            uasort($tree, array($this,'sortingGroups'));
        }
        $this->pdoTools->addTime('Group by Rule complete', microtime(true) - $time);
        return $tree;
    }


    public function multiSort($links = array()) {
        uasort($links,array($this,'sortingLinks'));
        foreach($links as $key=>$link) {
            if(isset($link['inner'])) {
                $inner = $this->multiSort($link['inner']);
                $links[$key]['inner'] = $inner;
            }
        }
        return $links;
    }

    public function sortingGroups($a,$b) {
        $groupsort = $this->config['groupsort'];
        $groupdir = $this->config['groupdir'];

        if ($a[$groupsort] == $b[$groupsort]) {
            if((int)$this->config['userank']) {
                if ($a['rank'] == $b['rank']) {
                    return 0;
                }
                return ($a['rank'] < $b['rank']) ? -1 : 1;
            } else {
                return 0;
            }
        } elseif ($groupdir == 'DESC') {
            return ($a[$groupsort] > $b[$groupsort]) ? -1 : 1;
        } else {
            return ($a[$groupsort] < $b[$groupsort]) ? -1 : 1;
        }
    }

    public function sortingLinks($a,$b) {
        $sortby = $this->config['sortby'];
        $sortdir = $this->config['sortdir'];
        $sortcount = (int)$this->config['sortcount'];
        $countChildren = (int)$this->config['countChildren'];
        if($sortcount && $countChildren) {
            $sortby = 'total';
        }
        if((int)$this->config['userank']) {
            if ($a['rank'] == $b['rank']) {
                if($sortby) {
                    if ($a[$sortby] == $b[$sortby]) {
                        return 0;
                    } else {
                        if($sortdir == 'DESC') {
                            return ($a[$sortby] > $b[$sortby]) ? -1 : 1;
                        } else {
                            return ($a[$sortby] < $b[$sortby]) ? -1 : 1;
                        }
                    }
                } else {
                    return 0;
                }
            }
            return ($a['rank'] < $b['rank']) ? -1 : 1;
        } elseif($sortby) {
            if ($a[$sortby] == $b[$sortby]) {
                return 0;
            } else {
                if($sortdir == 'DESC') {
                    return ($a[$sortby] > $b[$sortby]) ? -1 : 1;
                } else {
                    return ($a[$sortby] < $b[$sortby]) ? -1 : 1;
                }
            }
        } else {
            return 0;
        }
    }


    public function linkTemplate($row = array()) {
        $children = '';
        $row['level'] = $this->level;
        $relative = (int)$this->config['relative'];
        $active = (int)$this->config['hereId'];
        $find = 0;


        if (!empty($row['inner']) &&  ($this->isHere($row['id']) || empty($this->config['hideSubMenus']))) {
            $idx = $this->idx;
            $this->level++;
            $count = count($row['inner']);
            foreach ($row['inner'] as $v) {
                $v['idx'] = $idx++;
                $v['last'] = (int)$v['idx'] == $count;
                $children .= $this->linkTemplate($v);
            }
            $this->level--;
            $row['children'] = $count;
        } else {
            $row['children'] = isset($row['inner']) ? count($row['inner']) : 0;


        }


        if (!empty($children)) {
            $pls = array(
                'wrapper' => $children,
                'classes' => ' class="' .$this->config['innerClass'] . '"',
                'classnames' => $this->config['innerClass'],
                'classNames' => $this->config['innerClass'],
                'level' => $this->level,
            );
            $row['wrapper'] = $this->pdoTools->getChunk($this->config['tplInner'], $pls);
        } else {
            $row['wrapper'] = '';
        }

        $classes = $this->getClasses($row);

        if (!empty($classes)) {
            $row['classNames'] = $row['classnames'] = $classes;
            $row['classes'] = ' class="' . $classes . '"';
        } else {
            $row['classNames'] = $row['classnames'] = $row['classes'] = '';
        }

        $tpl = $this->getTpl($row);

        return $this->pdoTools->getChunk($tpl,$row,(int)$this->config['fastMode']);
    }

    public function getClasses($row = array())
    {
        $classes = array();

        if (!empty($this->config['rowClass'])) {
            $classes[] = $this->config['rowClass'];
        }
        if ($row['idx'] == 1 && !empty($this->config['firstClass'])) {
            $classes[] = $this->config['firstClass'];
        } elseif (!empty($row['last']) && !empty($this->config['lastClass'])) {
            $classes[] = $this->config['lastClass'];
        }
        if (!empty($this->config['levelClass'])) {
            $classes[] = $this->config['levelClass'] . $row['level'];
        }
        if ($row['children'] && !empty($this->config['parentClass']) && ($row['level'] < $this->config['level'] || empty($this->config['level']))) {
            $classes[] = $this->config['parentClass'];
        }
        $row_id = $row['id'];
        if ($this->isHere($row_id) && !empty($this->config['hereClass'])) {
            $classes[] = $this->config['hereClass'];
        }
        if ($row_id == $this->config['hereId'] && !empty($this->config['selfClass'])) {
            $classes[] = $this->config['selfClass'];
        }

        return implode(' ', $classes);
    }

    public function isHere($id = 0)
    {
        return in_array($id,$this->parents);
    }

    public function getTpl($row = array())
    {
        if ($row['children'] && $row['id'] == $this->config['hereId'] && !empty($this->config['tplParentRowHere'])) {
            $tpl = 'tplParentRowHere';
        } elseif ($row['level'] > 1 && $row['id'] == $this->config['hereId'] && !empty($this->config['tplInnerHere'])) {
            $tpl = 'tplInnerHere';
        } elseif ($row['id'] == $this->config['hereId'] && !empty($this->config['tplHere'])) {
            $tpl = 'tplHere';
        } elseif ($row['children'] && $this->isHere($row['id']) && !empty($this->config['tplParentRowActive'])) {
            $tpl = 'tplParentRowActive';
        } elseif ($row['children'] && !empty($this->pdoTools->config['tplParentRow'])) {
            $tpl = 'tplParentRow';
        } elseif ($row['level'] > 1 && !empty($this->pdoTools->config['tplInnerRow'])) {
            $tpl = 'tplInnerRow';
        } else {
            $tpl = 'tpl';
        }

        return $this->config[$tpl];
    }


    public function getTemplate($tree = array()) {
//        $this->tree = $tree;
        $idx = $this->idx;
        $count = count($tree);
        $output = '';
        foreach($tree as $row) {
            if (empty($row['id'])) {
                continue;
            }
            $this->level = 1;
            $row['idx'] = $idx++;
            $row['last'] = (integer)$row['idx'] == $count;

            $output .= $this->linkTemplate($row);
        }

        if(!empty($output)) {
                $output = $this->pdoTools->parseChunk($this->config['tplOuter'], array(
                        'wrapper' => $output,
                        'classes'=>' class="' . $this->config['outerClass'] . '"',
                        'classnames'=>$this->config['outerClass'],
                        'classNames'=>$this->config['outerClass'],
                    )
                );
        }

        return $output;

    }

    public function findParents($links = array(),$id = 0) {
        $parents = array();
        foreach($links as $row) {
            if($row['id'] == $id) {
                $parents[] = $row['id'];
                continue;
            }
            if(isset($row['childrens'])) {
                if(in_array($id,$row['childrens'])) {
                    $parents[] = $row['id'];
//                    continue;
                }
            }
            if(isset($row['inner'])) {
                if($inner = $this->findParents($row['inner'],$id)) {
                    $parents = array_merge($parents,$inner);
                    $parents[] = $row['id'];
                }

            }
        }
        return array_unique($parents);
    }


    public function makeMenu($tree = array(),$par = 0) {
        $output = '';
        $time = microtime(true);
        $this->pdoTools->addTime('Start template menu');

        if (isset($this->config['hereId'])) {
            $parents= $this->findParents($tree,(int)$this->config['hereId']);
//            $this->modx->log(1,print_r($parents,1));
            $this->parents = $parents;
//            $this->modx->log(1,print_r($this->parents,1));
        }


        $total = count($tree);
        if((int)$this->config['groupbyrule']) {
            $idx = 0;
            foreach($tree as $group) {
                if(isset($group['links'])) {
                    $idx++;
                    if ($total == $idx) {
                        $last = 1;
                    } else {
                        $last = 0;
                    }
                    if($idx == 1) {
                        $first = 1;
                    } else {
                        $first = 0;
                    }

                    $output .= $this->pdoTools->getChunk($this->config['tplGroup'], array_merge($group, array(
                            'idx' => $idx,
                            'last' => $last,
                            'first' => $first,
                            'wrapper' => $this->getTemplate($group['links'])))
                    );
                }
            }
        } else {

            if((int)$this->config['relative'] && $this->config['hereId']) {
                $find = 0;
                foreach($tree as $row) {
                    if(in_array($row['id'],$this->parents)) {
                        if(isset($row['inner'])) {
                            $find++;
                            $output = $this->getTemplate($row['inner']);
                        }
                    }
                }
            } else {
                $output = $this->getTemplate($tree);
            }

        }
        $this->pdoTools->addTime('End template menu',microtime(true) - $time);
        return $output;
    }




}