<?php

class sfSeoMetaGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfSeoMeta';
    public $classKey = 'sfSeoMeta';
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

        $array['seofield'] = $array['name'];
        if($array['field_id']) {
            $array['seofield'] = '<b>'.$this->modx->lexicon('seofilter_field').'</b>: '.$array['seofield'];
        }
        if($array['multi_id']) {
            $array['seofield'] = '<b>'.$this->modx->lexicon('seofilter_multifield').'</b>: '.$array['seofield'];
        }

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
            'title' => $this->modx->lexicon('seofilter_seometa_update'),
            //'multiple' => $this->modx->lexicon('seofilter_seometa_update'),
            'action' => 'updateField',
            'button' => true,
            'menu' => true,
        );

//        if (!$array['active']) {
//            $array['actions'][] = array(
//                'cls' => '',
//                'icon' => 'icon icon-power-off action-green',
//                'title' => $this->modx->lexicon('seofilter_seometa_enable'),
//                'multiple' => $this->modx->lexicon('seofilter_seometa_enable'),
//                'action' => 'enableField',
//                'button' => true,
//                'menu' => true,
//            );
//        } else {
//            $array['actions'][] = array(
//                'cls' => '',
//                'icon' => 'icon icon-power-off action-gray',
//                'title' => $this->modx->lexicon('seofilter_seometa_disable'),
//                'multiple' => $this->modx->lexicon('seofilter_seometa_disable'),
//                'action' => 'disableField',
//                'button' => true,
//                'menu' => true,
//            );
//        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('seofilter_seometa_remove'),
            'multiple' => $this->modx->lexicon('seofilter_seometa_remove'),
            'action' => 'removeField',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'sfSeoMetaGetListProcessor';