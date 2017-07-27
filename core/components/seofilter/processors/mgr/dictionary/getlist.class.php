<?php

class sfDictionaryGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
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
        $this->modx->log(modX::LOG_LEVEL_ERROR,print_r($this->getProperties(),1));
        $query = trim($this->getProperty('query'));
        if ($query) {
            $c->where(array(
                'input:LIKE' => "%{$query}%",
                'OR:value:LIKE' => "%{$query}%",
                'OR:alias:LIKE' => "%{$query}%",
            ));
        }

        if ($field = $this->getProperty('field',null)) {
            $c->andCondition(array('field_id' => $field), '', 1);
        }

        if ($class = $this->getProperty('class',null)) {
            $c->andCondition(array('class' => $class), '', 1);
        }

        $c->leftJoin('sfField', 'sfField', $this->classKey.'.field_id = sfField.id');
        $c->select($this->modx->getSelectColumns($this->classKey,$this->classKey));
        $c->select('sfField.name as fieldtitle');

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

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('seofilter_dictionary_update'),
            //'multiple' => $this->modx->lexicon('seofilter_dictionary_update'),
            'action' => 'updateField',
            'button' => true,
            'menu' => true,
        );
//
//        if (!$array['active']) {
//            $array['actions'][] = array(
//                'cls' => '',
//                'icon' => 'icon icon-power-off action-green',
//                'title' => $this->modx->lexicon('seofilter_dictionary_enable'),
//                'multiple' => $this->modx->lexicon('seofilter_dictionary_enable'),
//                'action' => 'enableField',
//                'button' => true,
//                'menu' => true,
//            );
//        } else {
//            $array['actions'][] = array(
//                'cls' => '',
//                'icon' => 'icon icon-power-off action-gray',
//                'title' => $this->modx->lexicon('seofilter_dictionary_disable'),
//                'multiple' => $this->modx->lexicon('seofilter_dictionary_disable'),
//                'action' => 'disableField',
//                'button' => true,
//                'menu' => true,
//            );
//        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('seofilter_dictionary_remove'),
            'multiple' => $this->modx->lexicon('seofilter_dictionary_remove'),
            'action' => 'removeField',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'sfDictionaryGetListProcessor';