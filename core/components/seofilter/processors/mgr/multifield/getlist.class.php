<?php

class sfMultiFieldGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfMultiField';
    public $classKey = 'sfMultiField';
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
                'name:LIKE' => "%{$query}%",
            ));
        }


        //if($this->getProperty('page')) {

            $c->leftJoin('modResource', 'modResource', $this->classKey.'.page = modResource.id');
            //$c->leftJoin('sfSeoMeta', 'sfSeoMeta', $this->classKey.'.id = sfSeoMeta.multi_id');
            $c->select($this->modx->getSelectColumns($this->classKey,$this->classKey));
            //$c->select($this->modx->getSelectColumns('sfSeoMeta','sfSeoMeta','',array('title','h1')));
            $c->select($this->modx->getSelectColumns('modResource','modResource','',array('pagetitle')));
          // $c->select('sfMultiField.*');
//            $c->select('modResource.pagetitle');
//            $c->select('sfSeoMeta.*');

        //}


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
            'title' => $this->modx->lexicon('seofilter_multifield_update'),
            //'multiple' => $this->modx->lexicon('seofilter_multifields_update'),
            'action' => 'updateField',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('seofilter_multifield_enable'),
                'multiple' => $this->modx->lexicon('seofilter_multifields_enable'),
                'action' => 'enableField',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('seofilter_multifield_disable'),
                'multiple' => $this->modx->lexicon('seofilter_multifields_disable'),
                'action' => 'disableField',
                'button' => true,
                'menu' => true,
            );
        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('seofilter_multifield_remove'),
            'multiple' => $this->modx->lexicon('seofilter_multifields_remove'),
            'action' => 'removeField',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'sfMultiFieldGetListProcessor';