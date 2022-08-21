<?php

class sfUrlWordGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfUrlWord';
    public $classKey = 'sfUrlWord';
    public $defaultSortField = 'priority';
    public $defaultSortDirection = 'ASC';
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
        // Выбираем только нужные записи
        $c->where(array('url_id' => $this->getProperty('url_id')));
        // И присоединяем свойства пользователей
        $c->leftJoin('sfDictionary', 'sfDictionary', $this->classKey.'.word_id = sfDictionary.id');
        $c->leftJoin('sfField', 'sfField', 'sfDictionary.field_id = sfField.id');
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select('sfField.name as field_name, sfField.alias as field_alias, sfDictionary.value as word_name, sfDictionary.alias as word_alias');


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

        //        $array['pagetitle'] = '';
        //        if ($page = $array['page']) {
        //            $q = $this->modx->newQuery('modResource', array('id' => $page));
        //            $q->select('pagetitle');
        //            $q->limit(1);
        //            if ($q->prepare() && $q->stmt->execute()) {
        //                $array['pagetitle'] = $q->stmt->fetch(PDO::FETCH_COLUMN);
        //            }
        //        }

        $array['actions'] = array();

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('seofilter_url_word_update'),
            //'multiple' => $this->modx->lexicon('seofilter_fieldids_update'),
            'action' => 'updateUrlWord',
            'button' => true,
            'menu' => true,
        );

        //        if (!$array['active']) {
        //            $array['actions'][] = array(
        //                'cls' => '',
        //                'icon' => 'icon icon-power-off action-green',
        //                'title' => $this->modx->lexicon('seofilter_fieldids_enable'),
        //                'multiple' => $this->modx->lexicon('seofilter_fieldids_enable'),
        //                'action' => 'enableUrlWord',
        //                'button' => true,
        //                'menu' => true,
        //            );
        //        } else {
        //            $array['actions'][] = array(
        //                'cls' => '',
        //                'icon' => 'icon icon-power-off action-gray',
        //                'title' => $this->modx->lexicon('seofilter_fieldids_disable'),
        //                'multiple' => $this->modx->lexicon('seofilter_fieldids_disable'),
        //                'action' => 'disableUrlWord',
        //                'button' => true,
        //                'menu' => true,
        //            );
        //        }

        // Remove
        //        $array['actions'][] = array(
        //            'cls' => '',
        //            'icon' => 'icon icon-trash-o action-red',
        //            'title' => $this->modx->lexicon('seofilter_fieldids_remove'),
        //            'multiple' => $this->modx->lexicon('seofilter_fieldids_remove'),
        //            'action' => 'removeUrlWord',
        //            'button' => true,
        //            'menu' => true,
        //        );

        return $array;
    }

}

return 'sfUrlWordGetListProcessor';
