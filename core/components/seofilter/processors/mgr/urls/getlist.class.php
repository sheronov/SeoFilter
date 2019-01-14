<?php

class sfUrlsGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfUrls';
    public $classKey = 'sfUrls';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';
    /*** @var SeoFilter $SeoFilter */
    protected $SeoFilter;


    public function initialize()
    {
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter',
            $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');
        return parent::initialize();
    }

    /**
     * We do a special check of permissions
     * because our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        if($this->getProperty('sort') == 'actions') {
            $this->setProperty('sort','active');
        }


        return true;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where(array(
                'link:LIKE' => "%{$query}%",
                'OR:id:LIKE' => "{$query}%",
                'OR:old_url:LIKE' => "%{$query}%",
                'OR:new_url:LIKE' => "%{$query}%",
            ));
        }

        if ($rule = $this->getProperty('rule',null)) {
            $c->andCondition(array('multi_id' => $rule), '', 1);
        }

        if ($page = $this->getProperty('page',null)) {
            $c->andCondition(array('page_id' => $page), '', 1);
        }

        if ($field = $this->getProperty('field',null)) {
            $cond = $this->classKey.'.id = sfUrlWord.url_id AND sfUrlWord.field_id = '.$field;
            if ($word = $this->getProperty('word',null)) {
                $cond .= ' AND sfUrlWord.word_id = '.$word;
            }
            $c->innerJoin('sfUrlWord','sfUrlWord',$cond);
        }



        $c->leftJoin('sfRule', 'sfRule', $this->classKey.'.multi_id = sfRule.id');
        $c->leftJoin('modResource', 'modResource', $this->classKey.'.page_id = modResource.id');
        $c->select(array($this->classKey.'.*','sfRule.page','sfRule.name as rule_name','modResource.pagetitle'));


        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {

        $array = $object->toArray();
        $array['actions'] = array();
        $array['menuon'] = array();

        if (!$array['menu_on']) {
            $array['menuon'][] = array(
                'cls' => '',
                'icon' => 'icon icon-toggle-off action-gray',
                'title' => $this->modx->lexicon('seofilter_menu_enable'),
                'multiple' => $this->modx->lexicon('seofilter_menu_enable'),
                'action' => 'enableMenu',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['menuon'][] = array(
                'cls' => '',
                'icon' => 'icon icon-toggle-on action-green',
                'title' => $this->modx->lexicon('seofilter_menu_disable'),
                'multiple' => $this->modx->lexicon('seofilter_menu_disable'),
                'action' => 'disableMenu',
                'button' => true,
                'menu' => true,
            );
        }

        if(($array['old_url'] || $array['new_url']) && $array['page_id']) {
            if(!($addurl = $array['new_url'])) {
                $addurl = $array['old_url'];
            }

            $page_url = $this->modx->makeUrl($array['page_id'],'','','full');
            $container_suffix = $this->modx->getOption('container_suffix',null,'/');
            $url_suffix = $this->modx->getOption('seofilter_url_suffix',null,'',true);
            $between_urls = $this->modx->getOption('seofilter_between_urls',null,'/',true);
            $main_alias = $this->modx->getOption('seofilter_main_alias',null,0);
            $site_start = $this->modx->context->getOption('site_start', 1);

            $url = $this->SeoFilter->clearSuffixes($page_url);

            if($site_start == $array['page_id']) {
                if($main_alias) {
                    $q = $this->modx->newQuery('modResource',array('id'=>$array['page_id']));
                    $q->select('alias');
                    $malias = $this->modx->getValue($q->prepare());
                    $array['url_preview'] = $url.'/'.$malias.$between_urls.$addurl.$url_suffix;
                } else {
                    $array['url_preview'] = $url.'/'.$addurl.$url_suffix;
                }
            } else {
                $array['url_preview'] = $url.$between_urls.$addurl.$url_suffix;
            }



            if(!$array['active']) {
                $addurl = array();

                $q = $this->modx->newQuery('sfUrlWord');
                $q->sortby('priority','ASC');
                $q->leftJoin('sfField','sfField','sfUrlWord.field_id = sfField.id');
                $q->leftJoin('sfDictionary','sfDictionary','sfUrlWord.word_id = sfDictionary.id');
                $q->where(array('sfUrlWord.url_id'=>(int)$array['id']));
                $q->select('sfUrlWord.id, sfField.id as field_id, sfField.alias as field_alias, sfField.hideparam, sfField.valuefirst, sfDictionary.input as word_input, sfDictionary.id as word_id, sfDictionary.alias as word_alias');
                if($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $addurl[] = $row['field_alias'].'='.$row['word_input'];
                    }
                }
                if(count($addurl)) {
                    $array['url_preview'] = $page_url . '?' . implode('&', $addurl);
                }
               // $this->modx->log(modX::LOG_LEVEL_ERROR,print_r($addurl,1));
            }

        }

        if (!empty($array['url_preview'])) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-eye',
                'title' => $this->modx->lexicon('seofilter_url_view'),
                'action' => 'viewPage',
                'button' => true,
                'menu' => true,
            );
        }

        if (!empty($array['url_preview'])) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-refresh',
                'title' => $this->modx->lexicon('seofilter_url_recount'),
                'multiple' => $this->modx->lexicon('seofilter_url_recount'),
                'action' => 'reCount',
                'button' => false,
                'menu' => true,
            );
        }

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('seofilter_url_update'),
            //'multiple' => $this->modx->lexicon('seofilter_url_update'),
            'action' => 'updateUrl',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('seofilter_url_enable'),
                'multiple' => $this->modx->lexicon('seofilter_url_enable'),
                'action' => 'enableUrl',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('seofilter_url_disable'),
                'multiple' => $this->modx->lexicon('seofilter_url_disable'),
                'action' => 'disableUrl',
                'button' => true,
                'menu' => true,
            );
        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('seofilter_url_remove'),
            'multiple' => $this->modx->lexicon('seofilter_url_remove'),
            'action' => 'removeUrl',
            'button' => true,
            'menu' => true,
        );





        return $array;
    }

}

return 'sfUrlsGetListProcessor';