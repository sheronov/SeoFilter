<?php
class sfRule extends xPDOSimpleObject {
    /** @var SeoFilter $SeoFilter */
    public $SeoFilter;
    public $config = array();


    public function save($cacheFlag = null)
    {
        $this->set('editedon',strtotime(date('Y-m-d H:i:s')));
        return parent::save($cacheFlag);
    }

    public function __construct(xPDO $xpdo)
    {
        parent::__construct($xpdo);
        if(!$this->SeoFilter) {
            $this->SeoFilter = $this->xpdo->getService('SeoFilter');
        }
    }

    public function makeUrl() {
        $url = array();
        $rule_id = (int)$this->get('id');
        $q = $this->xpdo->newQuery('sfFieldIds');
        $q->where(array('multi_id'=>$rule_id));
        $q->sortby('priority','ASC');
        $q->innerJoin('sfField','Field','Field.id = sfFieldIds.field_id');
        $q->select(array(
            'Field.*',
            'sfFieldIds.id as fid,sfFieldIds.priority'
        ));
        $fields = $this->xpdo->getIterator('sfField',$q);
        foreach($fields as $field) {
            /*** @var sfField $field */
            $url[] = $field->makeUrl();
        }

        $this->set('url',implode('/',$url));

        $this->save();
        return implode('/',$url);
    }

    /**
     * Returns true url for filter params
     *
     * @param int $returnArray = 0
     *
     * @return string
     */
    public function generateUrl($returnArray = 0,$word_arr = array()) {
        $link_tpl = $this->get('link_tpl');
        $seo_system = array('id','field_id','multi_id','name','rank','active','class','editedon','key');

        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);
        $url = '';
        $urls = array();
        $count = 1;
        $rule_id = $this->get('id');
        $q = $this->xpdo->newQuery('sfFieldIds');
        $q->sortby('priority', 'ASC');
        if($links = $this->getMany('Links',$q)){
            $countFields = count($links);
            foreach($links as $key => $link) {
                /* @var sfFieldIds $link */
                if($field = $link->getOne('Field')) {
                    $field_id = $field->get('id');
                    $aliases = array();
                    if($alias = $field->get('alias')) {
                        if($field->get('hideparam')) {
                            $url .= '{$'.$alias.'}/';
                        } elseif($field->get('valuefirst')){
                            $url .= '{$'.$alias.'}' . $separator . $alias. '/';
                        } else {
                            $url .= $alias . $separator . '{$'.$alias.'}/';
                        }
                        if($count == count($links)) {
                            $url = substr($url, 0,-1);
                        }

                        if($returnArray) {
                            $q = $this->xpdo->newQuery('sfDictionary');
                            $q->where(array('field_id' => $field_id));
//                            if($word_arr) {
//                                $q->where(array('id'=>$word_arr['id']));
//                            }

                            if($link->get('where') && $link->get('compare') && $link->get('value')) {
                                $value = $link->get('value');
                                $values = explode(',',$value);
                                switch ($link->get('compare')) {
                                    case 1:
                                        $q->where(array('input:IN'=>$values));
                                        break;
                                    case 2:
                                        $q->where(array('input:NOT IN'=>$values));
                                        break;
                                    case 3:
                                        $q->where(array('input:>'=>$value));
                                        break;
                                    case 4:
                                        $q->where(array('input:<'=>$value));
                                        break;
                                    case 5:
                                        $q->where(array('input:>'=>$values[0],'AND:input:<'=>$values[1]));
                                        break;
                                }
                            }

                            $words = $field->getMany('Words',$q);
                            foreach($words as $word) {
                                /* @var sfDictionary $word */
//                                $this->xpdo->log(1,print_r($word->toArray(),1));
                                $word_arr = $word->toArray();
                                $word_array = array();
                                foreach(array_diff_key($word_arr, array_flip($seo_system)) as $tmp_key => $tmp_array) {
                                    if($countFields == 1) {
                                        $word_array[$tmp_key] = $tmp_array;
                                    }
                                    $word_array[str_replace('value',$alias,$tmp_key)] = $tmp_array;
                                    $word_array[$alias.'_input'] = $word_array['input'];
                                    if(isset($word_array['alias'])) {
                                        $word_array[$alias.'_alias'] = $word_array['alias'];
                                    }
                                    if(isset($word_array['m_'.$alias.'_i'])) {
                                        $word_array['m_' . $alias] = $word_array['m_' . $alias . '_i'];
                                    }
                                }


                                $all_array = array(
                                    'url' => $word->get('alias'),
                                    'link' => $link_tpl,
                                    'word_array' => $word_array,

                                    'field_relation' => $field->get('relation_field'),
                                    'word_relation' => $word->get('relation_word'),

                                    'relation' => array(
                                        array(
                                            'field_id' => $field_id,
                                            'word_id' => $word->get('id'),
                                            'field_relation' => $field->get('relation_field'),
                                            'word_relation' => $word->get('relation_word'),
                                        )
                                    ),

                                    'field_word' => array(array(
                                        'field_id' => $field_id,
                                        'word_id' => $word->get('id'),
                                        //'word_alias' => $word->get('alias'),
                                    )),

                                    'delete' => 0,
                                );
                                $aliases[$key][] = $all_array;
                                //$aliases[$key][] = $word->get('alias');
                            }
                            foreach($aliases[$key] as $akey => $avalue) {
                                if($field->get('hideparam')) {
                                    $add_url = $avalue['url'] . '/';
                                } elseif($field->get('valuefirst')){
                                    $add_url =  $avalue['url'] . $separator .$alias.'/';
                                } else {
                                    $add_url = $alias . $separator .$avalue['url'].'/';
                                }
                                if($count == count($links)) {
                                    $add_url = substr($add_url, 0,-1);
                                }
                                $avalue['url'] = $add_url;
                                $urls[$key][] = $avalue;
                                //$urls[$key][] = $add_url;
                            }
                        }
                        $count++;
                    }
                }
            }
        }
        if(!$returnArray) {
            return $url;
        } else {
            $urls = array_values($urls);
            $urls_array = array();
            if(count($urls)) {
                $urls_array = array_shift($urls);
                foreach($urls as $url_arr) {
                    $urls_array = $this->matrixmult($urls_array,$url_arr);
                }
            }
//            $this->xpdo->log(1,'Urls '.print_r($urls_array,1));
            foreach($urls_array as $key=> $url_array) {
                if($url_array['delete']) {
                    unset($urls_array[$key]);
                    continue;
                }
                $urls_array[$key]['link'] = $this->SeoFilter->pdo->getChunk('@INLINE '.$url_array['link'], $url_array['word_array']);
                unset($urls_array[$key]['word_array']);
            }
            return $urls_array;
        }
    }



    public function matrixmult($a1,$a2) {
        $r=count($a1);
        $c=count($a2);
        $a3 = array();

        for ($i=0;$i< $r;$i++){
            for($j=0;$j<$c;$j++){

                $delete = $a1[$i]['delete'] || $a2[$j]['delete'];
                $arr = array(
                    'url' => $a1[$i]['url'] . $a2[$j]['url'],
                    'link' => $a1[$i]['link'],
                    'word_array' => array_merge($a1[$i]['word_array'], $a2[$j]['word_array']),
                    'field_word' => array_merge($a1[$i]['field_word'], $a2[$j]['field_word']),
                    'relation' => array_merge($a1[$i]['relation'], $a2[$j]['relation']),
                    'delete'=> $delete
                );

                $find = 1;
                if($a2[$j]['field_relation'] && $a2[$j]['word_relation']) {
                    $find = 0;
                    foreach($a1[$i]['relation'] as $relation) {
                        if(($relation['field_id'] == $a2[$j]['field_relation']) && ($relation['word_id'] == $a2[$j]['word_relation'])) {
                            $find = 1;
                            break;
                        }
                    }
                }

                if(!$find) {
                    $arr['delete'] = 1;
                }


                $a3[] = $arr;
            }
        }
        return $a3;
    }




}