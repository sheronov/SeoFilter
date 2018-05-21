<?php

class sfRuleReCountProcessor extends modObjectProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';

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
            return $this->failure($this->modx->lexicon('seofilter_rule_err_ns'));
        }

        /* @var SeoFilter $SeoFilter */
        $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', array());
        $SeoFilter->loadHandler();
        $total_message = '';

        foreach($ids as $rid) {
            if ($counts = $SeoFilter->countHandler->countByRule($rid)) {
                $counts['rule_id'] = $rid;
                $total_message .= $SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_rule_recount_message'), $counts);
            }
        }

//        $this->modx->log(1,print_r($total_message,1));

        return $this->success($total_message);
    }

}

return 'sfRuleReCountProcessor';