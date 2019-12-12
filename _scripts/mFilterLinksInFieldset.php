<?php

class seoFilterHandler extends mse2FiltersHandler
{

    const PAGE_ID = 6;
    /** @var modX $modx */
    public $modx;

    protected $seoAliases = [];
    protected $fields     = [];
    protected $rules      = [];
    protected $links      = [];
    protected $requested  = [];
    protected $fieldWords = [];
    protected $pageUrl    = '';
    protected $suffix     = '';


    public function __construct(mSearch2 $mse2, array $config = [])
    {
        parent::__construct($mse2, $config);
        $this->seoAliases = ['naznachenie', 'sezon', 'floor', 'area', 'dimensions', 'dop', 'bedroom'];
        $this->modx->addPackage('seofilter', $this->modx->getOption('seofilter_core_path', $config,
                $this->modx->getOption('core_path').'components/seofilter/').'model/');
        $this->fields = $this->getSeoFilterFields();
        $this->rules = $this->selectRulesFromFields();
        $this->requested = $this->prepareRequest();
        $this->pageUrl = $this->getPageUrl(self::PAGE_ID);
        $this->suffix = $this->modx->getOption('seofilter_url_suffix', null, '', true);
    }

    protected function getPageUrl($pageId)
    {
        $page_url = $this->modx->makeUrl($pageId);

        $c_suffix = $this->modx->getOption('container_suffix', null, '/');
        $possibleSuffixes = array_map('trim', explode(',',
            $this->modx->getOption('seofitler_possible_suffixes', null, '/,.html,.php', true)));
        $possibleSuffixes = array_unique(array_merge($possibleSuffixes, [$c_suffix]));

        foreach ($possibleSuffixes as $possibleSuffix) {
            if (substr($page_url, -strlen($possibleSuffix)) === $possibleSuffix) {
                $page_url = substr($page_url, 0, -strlen($possibleSuffix));
            }
        }

        return $page_url;
    }

    protected function getSeoFilterFields()
    {
        $fields = [];
        $q = $this->modx->newQuery('sfField');
        $q->leftJoin('sfFieldIds', 'Link', 'sfField.id = Link.field_id');
        $q->leftJoin('sfRule', 'Rule', 'Link.multi_id = Rule.id');
        $q->select($this->modx->getSelectColumns('sfField', 'sfField'));
        $q->select(['group_concat(Rule.id ORDER BY Rule.rank) as rules']);
        $q->select(['group_concat(Rule.rank ORDER BY Rule.rank) as rule_ranks']);
        $q->groupby('sfField.id');
        $q->where(['alias:IN' => $this->seoAliases]);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($field = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (isset($field['rules']) && !empty($field['rules'])) {
                    $field['rules'] = explode(',', $field['rules']);
                    $field['rule_ranks'] = explode(',', $field['rule_ranks']);
                } else {
                    $field['rules'] = [];
                    $field['rule_ranks'] = [];
                }
                $fields[$field['alias']] = $field;
            }
        }


        return $fields;
    }

    protected function selectRulesFromFields()
    {
        $rules = [];

        foreach ($this->fields as $alias => $field) {
            if (!empty($field['rules'])) {
                foreach ($field['rules'] as $pos => $rule) {
                    $rules[$rule]['rank'] = $field['rule_ranks'][$pos];
                    $rules[$rule]['fields'][] = $field['id'];
                }
            }
        }

        uasort($rules, function ($r1, $r2) {
            return $r1['rank'] >= $r2['rank'];
        });

        return $rules;
    }

    protected function prepareRequest()
    {
        $requested = [];

        foreach ($_REQUEST as $param => $values) {
            if (!in_array($param, $this->seoAliases, true)) {
                continue;
            }
            if (strpos($values, ',') !== false) {
                continue;
            }

            $requested[$param] = $values;
        }

        return $requested;
    }

    public function getTvValues(array $tvs, array $ids)
    {
        $filters = parent::getTvValues($tvs, $ids);

        $fields = [];
        foreach ($filters as $fieldAlias => $values) {
            $fields[$fieldAlias] = array_keys($values);
        }

        $this->getFieldWords($fields);

        return $filters;
    }

    protected function getFieldWords(array $collectedFields = [])
    {
        $fieldWords = [];
        $collectedWords = [];
        if (!empty($collectedFields)) {
            $wordInputs = [];
            foreach ($collectedFields as $k => $values) {
                $wordInputs = array_merge($wordInputs, $collectedFields);
            }
            // $wordInputs = array_merge(...array_values($collectedFields));
        } else {
            $wordInputs = [];
        }
        if (!empty($wordInputs)) {
            $q = $this->modx->newQuery('sfDictionary');
            $q->where(['input:IN' => $wordInputs]);
            $q->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', '', ['field_id', 'input', 'id']));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($word = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $fieldWords[$word['field_id']][$word['input']] = $word['id'];
                    $collectedWords[$word['id']] = $word['input'];
                }
            }
        }

        $this->fieldWords = $fieldWords;

        $this->getSeoLinks($collectedWords);
    }

    protected function getSeoLinks(array $wordIds = [])
    {
        if (empty($this->requested) || count($this->requested) > 1) {
            $this->getSimpleSeoLinks(array_combine(array_keys($wordIds), array_keys($wordIds)));
        } else {
            $fieldAlias = array_keys($this->requested)[0];
            $wordInput = $this->requested[$fieldAlias];
            if (strpos($wordInput, ',') !== false) {
                $this->getSimpleSeoLinks(array_combine(array_keys($wordIds), array_keys($wordIds)));
            } else {
                $this->getHardSeoLinks($wordIds, $fieldAlias, $wordInput);
            }
        }
    }

    protected function getHardSeoLinks(array $wordIds, $fieldAlias, $wordInput)
    {
        $fieldId = $wordId = null;
        $doubleRules = [];
        if (isset($this->fields[$fieldAlias])) {
            $fieldId = $this->fields[$fieldAlias]['id'];
            $doubleRules = $this->fields[$fieldAlias]['rules'];
        }

        if ($fieldId) {
            if (isset($this->fieldWords[$fieldId][$wordInput])) {
                $wordId = $this->fieldWords[$fieldId][$wordInput];
            }
        }


        $glueFields = [];

        if (!empty($doubleRules)) {
            foreach ($doubleRules as $doubleRule) {
                if (isset($this->rules[$doubleRule]['fields']) && count($this->rules[$doubleRule]['fields']) === 2) {
                    foreach ($this->rules[$doubleRule]['fields'] as $field) {
                        if ($field !== $fieldId) {
                            $glueFields[$field] = [];
                        }
                    }
                }
            }
        }

        $links = [];
        if (!empty($glueFields)) {
            foreach ($this->fieldWords as $field => $words) {
                foreach ($words as $wInput => $wId) {
                    if (!isset($glueFields[$field])) {
                        $links[(string)$wId] = $wId;
                    } else {
                        $pair = [$wordId, $wId];
                        sort($pair);
                        $glueFields[$field][] = $pair;
                        $links[implode(',', $pair)] = $wId;
                    }
                }
            }
        }

        $this->getSimpleSeoLinks($links);
    }

    protected function getSimpleSeoLinks(array $wordIds = [])
    {
        $links = [];
        $q = $this->modx->newQuery('sfUrls');
        $q->leftJoin('sfUrlWord', 'Words', 'sfUrls.id = Words.url_id');
        $q->where(['active' => 1, 'page_id' => self::PAGE_ID, 'total:>=' => 3]);
        $q->groupby('sfUrls.id');
        $q->select($this->modx->getSelectColumns('sfUrls', 'sfUrls', '',
            ['id', 'multi_id', 'old_url', 'new_url', 'menutitle', 'link', 'total']));
        $q->select(['GROUP_CONCAT(Words.word_id ORDER BY Words.word_id) as wordids']);
        $q->having('wordids IN ('.implode(',', array_map(function ($value) {
                if (is_array($value)) {
                    sort($value);
                    $value = implode(',', $value);
                }
                return "'".$value."'";
            }, array_keys($wordIds))).')');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($link = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $links[$wordIds[$link['wordids']]] = $link;
            }
        }

        $this->links = $links;
    }

    protected function prepareLink(array $link, $as_name = '')
    {
        $url = $this->pageUrl.'/'.($link['new_url'] ?: $link['old_url']).$this->suffix;
        $name = $as_name ?: $link['menutitle'] ?: $link['link'];

        return strtr('<a href="{$url}">{$name}</a>', ['{$url}' => $url, '{$name}' => $name]);
    }

    public function buildSeoLinkFilter(array $values, $name = '')
    {
        if (count($values) < 2 && empty($this->config['showEmptyFilters'])) {
            return [];
        }

        $fieldId = null;
        if (isset($this->fields[$name])) {
            $fieldId = $this->fields[$name]['id'];
        }

        $results = [];
        foreach ($values as $value => $ids) {
            if ($value !== '') {
                $title = $value;
                if ($fieldId) {
                    if (isset($this->fieldWords[$fieldId][$value])) {
                        $wordId = $this->fieldWords[$fieldId][$value];
                        if (isset($this->links[$wordId])) {
                            $title = $this->prepareLink($this->links[$wordId], $value);
                        }
                    }
                }
                $results[$value] = [
                    'title'     => $title,
                    'value'     => $value,
                    'type'      => 'default',
                    'resources' => $ids
                ];
            }
        }

        return $this->sortFilters($results, 'default', ['name' => $name]);
    }

    public function _buildDefaultFilter(array $values, $name = '')
    {
        if (count($values) < 2 && empty($this->config['showEmptyFilters'])) {
            return [];
        }

        $results = [];
        foreach ($values as $value => $ids) {
            if ($value !== '') {
                $results[$value] = [
                    'title'     => $value,
                    'value'     => $value,
                    'type'      => 'default',
                    'resources' => $ids
                ];
            }
        }

        return $this->sortFilters($results, 'default', ['name' => $name]);
    }

}