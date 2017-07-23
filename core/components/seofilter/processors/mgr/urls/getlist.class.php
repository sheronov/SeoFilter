<?php

class sfUrlsGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfUrls';
    public $classKey = 'sfUrls';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';


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
                'old_url:LIKE' => "%{$query}%",
                'OR:new_url:LIKE' => "%{$query}%",
            ));
        }

        if ($rule = $this->getProperty('rule',null)) {
            $c->andCondition(array('multi_id' => $rule), '', 1);
        }

        if ($page = $this->getProperty('page',null)) {
            $c->andCondition(array('page_id' => $page), '', 1);
        }

        $c->leftJoin('sfRule', 'sfRule', $this->classKey.'.multi_id = sfRule.id');
        $c->leftJoin('modResource', 'modResource', $this->classKey.'.page_id = modResource.id');
        $c->select(array($this->classKey.'.*','sfRule.page','sfRule.name','modResource.pagetitle'));


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

        if(($array['old_url'] || $array['new_url']) && $array['page_id']) {
            if(!($addurl = $array['new_url'])) {
                $addurl = $array['old_url'];
            }

            $array['url_preview'] = $this->modx->makeUrl($array['page_id']).$addurl;

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
                    $array['url_preview'] = $this->modx->makeUrl($array['page_id']) . '?' . implode('&', $addurl);
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

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('seofilter_url_update'),
            //'multiple' => $this->modx->lexicon('seofilter_url_update'),
            'action' => 'updateField',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('seofilter_url_enable'),
                'multiple' => $this->modx->lexicon('seofilter_url_enable'),
                'action' => 'enableField',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('seofilter_url_disable'),
                'multiple' => $this->modx->lexicon('seofilter_url_disable'),
                'action' => 'disableField',
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
            'action' => 'removeField',
            'button' => true,
            'menu' => true,
        );





        return $array;
    }

}

return 'sfUrlsGetListProcessor';