<?php

class sfRuleGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';
    public $proMode = 0;

    public function initialize()
    {
        $this->proMode = (int)$this->modx->getOption('seofilter_pro_mode', null, 0);

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
                'OR:id:LIKE' => "%{$query}%",
                'OR:url:LIKE' => "%{$query}%",
                'OR:title:LIKE' => "%{$query}%",
            ));
        }
        
        if ($page = $this->getProperty('page',null)) {
            $c->andCondition(array('page' => $page), '', 1);
        }

        //if($this->getProperty('page')) {

            $c->leftJoin('modResource', 'modResource', $this->classKey . '.page = modResource.id');
            $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
            $c->select($this->modx->getSelectColumns('modResource', 'modResource', '', array('pagetitle')));

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
            'title' => $this->modx->lexicon('seofilter_rule_update'),
            //'multiple' => $this->modx->lexicon('seofilter_rules_update'),
            'action' => 'updateField',
            'button' => true,
            'menu' => true,
        );

        // Duplicate
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-files-o',
            'title' => $this->modx->lexicon('seofilter_rule_duplicate'),
            'action' => 'duplicateRule',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('seofilter_rule_enable'),
                'multiple' => $this->modx->lexicon('seofilter_rules_enable'),
                'action' => 'enableField',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('seofilter_rule_disable'),
                'multiple' => $this->modx->lexicon('seofilter_rules_disable'),
                'action' => 'disableField',
                'button' => true,
                'menu' => true,
            );
        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('seofilter_rule_remove'),
            'multiple' => $this->modx->lexicon('seofilter_rules_remove'),
            'action' => 'removeField',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'sfRuleGetListProcessor';