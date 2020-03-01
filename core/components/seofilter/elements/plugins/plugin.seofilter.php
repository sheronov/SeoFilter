<?php
/** @var modX $modx */

/** @var array $scriptProperties */
switch ($modx->event->name) {
    case 'OnLoadWebDocument':
        if ($page = $modx->resource->id) {
            $modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
            $proMode = $modx->getOption('seofilter_pro_mode', null, 0, true);
            $q = $modx->newQuery('sfRule');
            $q->where(['active' => 1]);
            if ($proMode) {
                $q->where('1=1 AND FIND_IN_SET('.$page.',REPLACE(IFNULL(NULLIF(pages,""),page)," ",""))');
            } else {
                $q->where(['page' => $page]);
            }
            if ($modx->getCount('sfRule', $q)) {
                $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                        $modx->getOption('core_path').'components/seofilter/').'model/seofilter/',
                    $scriptProperties);
                if (!($SeoFilter instanceof SeoFilter)) {
                    break;
                }
                $SeoFilter->initialize($modx->resource->context_key, ['page' => $page]);
            }
        }
        break;
    case 'OnBeforeDocFormSave':
        if (!$sf_count = $modx->getOption('seofilter_count', null, 0, true)) {
            break;
        }

        if (!$modx->getOption('seofilter_collect_words', null, 1)) {
            break;
        }

        $sf_classes = $modx->getOption('seofilter_classes', null, 'msProduct', true);
        if ($sf_classes && !in_array($resource->get('class_key'), array_map('trim', explode(',', $sf_classes)), true)) {
            break;
        }
        $sf_templates = $modx->getOption('seofilter_templates', null, '', true);
        if ($sf_templates && !in_array($resource->get('template'), array_map('trim', explode(',', $sf_templates)),
                true)) {
            break;
        }

        $before = [];
        if ($mode === 'upd') {
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                    $modx->getOption('core_path').'components/seofilter/').'model/seofilter/', $scriptProperties);
            if (!($SeoFilter instanceof SeoFilter)) {
                break;
            }

            $fields = $SeoFilter->getFieldsKey();
            $before = $SeoFilter->getResourceData($resource->id, $fields);
        }
        $_SESSION['SeoFilter']['before'] = $before;
        // @session_write_close();
        break;
    case 'OnDocFormSave':
        if (!$modx->getOption('seofilter_collect_words', null, 1)) {
            break;
        }
        $sf_classes = $modx->getOption('seofilter_classes', null, 'msProduct', true);
        if ($sf_classes && !in_array($resource->get('class_key'), array_map('trim', explode(',', $sf_classes)), true)) {
            break;
        }
        $sf_templates = $modx->getOption('seofilter_templates', null, '', true);
        if ($sf_templates && !in_array($resource->get('template'), array_map('trim', explode(',', $sf_templates)),
                true)) {
            break;
        }

        $sf_count = $modx->getOption('seofilter_count', null, 0, true);
        $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                $modx->getOption('core_path').'components/seofilter/').'model/seofilter/', $scriptProperties);
        $pdo = $SeoFilter->pdo;
        if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) {
            break;
        }

        $dictionary = $changes = $before = [];
        $fields = $SeoFilter->getFieldsKey();
        $after = $SeoFilter->getResourceData($resource->id, $fields);

        if (array_key_exists('tagger', $fields)) {
            $taggerPath = $modx->getOption('tagger.core_path', null,
                $modx->getOption('core_path', null, MODX_CORE_PATH).'components/tagger/');
            /** @var Tagger $tagger */
            $tagger = $modx->getService('tagger', 'Tagger', $taggerPath.'model/tagger/',
                ['core_path' => $taggerPath]);
            if (($tagger instanceof Tagger)) {
                $q = $modx->newQuery('TaggerGroup');
                $q->where(['id:IN' => array_keys($fields['tagger']), 'OR:alias:IN' => array_keys($fields['tagger'])]);
                $q->select('id,alias');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($group = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        if ($resource->get('tagger-'.$group['id'])) {
                            if (array_key_exists($group['id'], $fields['tagger'])) {
                                $after['tagger'][$group['id']] = $tagger->explodeAndClean($resource->get('tagger-'.$group['id']));
                            } else {
                                $after['tagger'][$group['alias']] = $tagger->explodeAndClean($resource->get('tagger-'.$group['id']));
                            }
                        }
                    }
                }
            }
        }

        if ($sf_count) {
            $SeoFilter->loadHandler();
            $before = $_SESSION['SeoFilter']['before'];
            unset($_SESSION['SeoFilter']);
        }

        foreach (['tvs', 'tvss', 'data', 'tagger'] as $var) {
            $dictionary = $SeoFilter->returnChanges($after[$var], [], $var);

            if (!empty($after[$var])) {
                if (!empty($before[$var])) {
                    if ($change = $SeoFilter->returnChanges($before[$var], $after[$var], $var)) {
                        $changes[$var] = $change;
                    }
                    // сравнить значения
                } elseif ($change = $SeoFilter->returnChanges($after[$var], [], $var)) {
                    $changes[$var] = $change;
                }
            } elseif (!empty($before[$var])) {
                if ($change = $SeoFilter->returnChanges($before[$var], [], $var)) {
                    $changes[$var] = $change;
                }
                // удалились значения
            }


            if (!empty($dictionary)) {
                foreach ($dictionary as $field_key => $words) {
                    foreach ($words as $word) {
                        if (empty($word)) {
                            continue;
                        }
                        $slider = (int)$fields[$var][$field_key]['slider'];
                        if ($word_array = $SeoFilter->getWordArray($word, $fields[$var][$field_key]['id'], 0,
                            !$slider)) {
                            if ($sf_count && is_array($changes[$var][$field_key])
                                && in_array($word, $changes[$var][$field_key], true)) {
                                $recount = $SeoFilter->countHandler->countByWord($word_array['id']);
                            }
                        } elseif ($sf_count && $slider && is_array($changes[$var][$field_key])
                            && in_array($word, $changes[$var][$field_key], true)) {
                            $recount = $SeoFilter->countHandler->countBySlider($fields[$var][$field_key]['id'],
                                $fields[$var][$field_key]);
                        }
                        if (is_array($changes[$var][$field_key])) {
                            foreach ($changes[$var][$field_key] as $key => $value) {
                                if ($word === $value) {
                                    unset($changes[$var][$field_key][$key]);
                                }
                                //здесь пересчитали только новые значения
                            }
                        }
                    }
                }
            }


            if (!empty($changes[$var]) && $sf_count) {
                // действия на пересчёт слов, которые удалены
                foreach ($changes[$var] as $field_key => $words) {
                    foreach ($words as $_word) {
                        if (is_array($_word)) {
                            $word_arr = $_word;
                        } else {
                            $word_arr = [$_word];
                        }
                        foreach ($word_arr as $word) {
                            if (empty($word)) {
                                continue;
                            }
                            $slider = (int)$fields[$var][$field_key]['slider'];
                            if ($word_array = $SeoFilter->getWordArray($word, $fields[$var][$field_key]['id'], 0,
                                !$slider)) {
                                $recount = $SeoFilter->countHandler->countByWord($word_array['id']);
                            } elseif ($slider) {
                                $recount = $SeoFilter->countHandler->countBySlider($fields[$var][$field_key]['id'],
                                    $fields[$var][$field_key]);
                            }
                        }
                    }
                }
            }
        }

        break;
    case 'OnPageNotFound':
        $alias = $modx->context->getOption('request_param_alias', 'q');

        if (isset($_REQUEST[$alias])) {
            /** @var SeoFilter $SeoFilter */
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                    $modx->getOption('core_path').'components/seofilter/').'model/seofilter/', $scriptProperties);
            if (!($SeoFilter instanceof SeoFilter)) {
                break;
            }

            $SeoFilter->processUrl(trim($_REQUEST[$alias]));
        }
        break;
}