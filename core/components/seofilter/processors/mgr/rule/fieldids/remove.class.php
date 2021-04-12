<?php

class sfFieldIdsRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'seofilter.rule_field';
    public $classKey = 'sfFieldIds';
    public $languageTopics = array('seofilter');
    //public $permission = 'remove';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('seofilter_fieldids_err_ns'));
        }
        $rule_id = 0;
        foreach ($ids as $id) {
            /** @var sfField $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_fieldids_err_nf'));
            }

            $rule_id = $object->get('multi_id');

            $object->remove();
        }


        if($rule_id && $rule = $this->modx->getObject('sfRule',$rule_id)) {
            $url = $rule->updateUrlMask();
        } else {
            $url = $this->updateUrlMask($rule_id);
        }

        return $this->success($url);
    }

    public function updateUrlMask($rule_id = 0) {
        $separator = $this->modx->getOption('seofilter_separator', null, '-', true);
        $level_separator = $this->modx->getOption('seofilter_level_separator', null, '/', true);

        $urls = array();
        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(array('multi_id'=>$rule_id));
        $q->sortby('priority','ASC');
        $q->innerJoin('sfField','Field','Field.id = sfFieldIds.field_id');
        $q->select(array(
            'Field.*'
        ));
        if($q->prepare() && $q->stmt->execute()) {
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $var = '{$'.$row['alias'].'}';

                if($row['hideparam']) {
                    $urls[] = $var;
                } elseif($row['valuefirst']) {
                    $urls[] = $var.$separator.$row['alias'];
                } else {
                    $urls[] = $row['alias'].$separator.$var;
                }
            }
        }

        $url = implode($level_separator,$urls);

        return $url;
    }

}

return 'sfFieldIdsRemoveProcessor';