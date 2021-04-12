<?php

class sfFieldGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'seofilter.field';
    public $classKey = 'sfField';
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
                'name:LIKE' => "%{$query}%",
                'OR:class:LIKE' => "%{$query}%",
                'OR:key:LIKE' => "%{$query}%",
                'OR:alias:LIKE' => "%{$query}%",
            ));

        }





        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        if ($this->getProperty('combo')) {
            $array = array(
                'id' => $object->get('id'),
                'name' => "({$object->get('id')}) ".$object->get('name'),
            );
        } else {
            $array = $object->toArray();

            $array['actions'] = array();

            // Edit
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('seofilter_field_update'),
                //'multiple' => $this->modx->lexicon('seofilter_fields_update'),
                'action' => 'updateField',
                'button' => true,
                'menu' => true,
            );

            if (!$array['active']) {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-green',
                    'title' => $this->modx->lexicon('seofilter_field_enable'),
                    'multiple' => $this->modx->lexicon('seofilter_fields_enable'),
                    'action' => 'enableField',
                    'button' => true,
                    'menu' => true,
                );
            } else {
                $array['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-gray',
                    'title' => $this->modx->lexicon('seofilter_field_disable'),
                    'multiple' => $this->modx->lexicon('seofilter_fields_disable'),
                    'action' => 'disableField',
                    'button' => true,
                    'menu' => true,
                );
            }

            // Remove
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-trash-o action-red',
                'title' => $this->modx->lexicon('seofilter_field_remove'),
                'multiple' => $this->modx->lexicon('seofilter_fields_remove'),
                'action' => 'removeField',
                'button' => true,
                'menu' => true,
            );
        }

        return $array;
    }

}

return 'sfFieldGetListProcessor';