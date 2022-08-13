<?php
/** @var modX $modx */
/** @var array $scriptProperties */
switch ($modx->event->name) {
    case 'OnLoadWebDocument':
        if(!empty($_REQUEST['q']) && $_REQUEST['q'] !== mb_strtolower($_REQUEST['q'])) {
            $modx->sendRedirect('https://' . $_SERVER['HTTP_HOST'] . '/' . mb_strtolower($_REQUEST['q']), ['responseCode' => 'HTTP/1.1 301 Moved Permanently']);
        }

        if($page = $modx->resource->id) {
            $page = 2;
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                    $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            if (!($SeoFilter instanceof SeoFilter)) break;
            $q = $modx->newQuery('sfRule');
            $q->where(array('page' => $page)); // Одно правило для одной страницы!
            if($modx->getCount('sfRule',$q)) {
                if(!$SeoFilter->initialized[$modx->context->key]) {
                    $SeoFilter->initialize($modx->context->key, array('page' => (int)$page), true);
                }
            }
        }
        if ($modx->resource->id === 2 && array_key_exists('query', $_REQUEST) && empty($_REQUEST['query'])) {
            $modx->sendRedirect($modx->makeUrl(2, '', '', 'full'));
        }
        break;
    case 'OnDocFormSave':
        $sf_classes = $modx->getOption('seofilter_classes', null, 'msProduct', true);
        if($sf_classes &&!in_array($resource->get('class_key'),array_map('trim', explode(',',$sf_classes))))
            break;
        $sf_templates = $modx->getOption('seofilter_templates', null, '', true);
        if($sf_templates && !in_array($resource->get('template'),array_map('trim', explode(',',$sf_templates))))
            break;

        $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
        $pdo = $SeoFilter->pdo;
        if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) break;

        $r_array = $resource->toArray();
        $fields = $pdo->getCollection('sfField');
        foreach($fields as $field) {
            if($field['active'] && !$field['slider']) {
                $key = $field['key'];
                $input = '';
                switch ($field['class']) {
                    case 'msProductOption':
                        $input = $r_array['options'][$key];
                        if (!$input)
                            $input = $r_array[$key];
                        break;
                    case 'msProductData':
                    case 'modResource':
                        $input = $r_array[$key];
                        break;
                    case 'modTemplateVar':
                        $input = $resource->getTVValue($key);
                        break;
                    case 'msVendor':
                        $input = $r_array['vendor.id'];
                        break;
                    default:
                        break;
                }
                if (is_array($input)) {
                    foreach ($input as $inp) {
                        $word_array = $SeoFilter->getWordArray($inp, $field['id']);
                    }
                } elseif ($input) {
                    if (strpos($input, '||')) {
                        foreach (array_map('trim', explode('||', $input)) as $inp) {
                            $word_array = $SeoFilter->getWordArray($inp, $field['id']);
                        }
                    } elseif (strpos($input, ',')) {
                        foreach (array_map('trim', explode(',', $input)) as $inp) {
                            $word_array = $SeoFilter->getWordArray($inp, $field['id']);
                        }
                    } else {
                        $word_array = $SeoFilter->getWordArray($input, $field['id']);
                    }
                }
            }

        }

        break;
    case 'OnPageNotFound':
        $time = microtime(true);
        $alias = $del_get = $modx->context->getOption('request_param_alias', 'q');

        if (isset($_REQUEST[$alias])) {
            if ($_REQUEST[$alias] === 'index.html') {
                $modx->sendRedirect('/');
            }

            if (strrpos($_REQUEST[$alias], 'homes') !== 0 || strpos($_REQUEST[$alias], 'homes') !== 0 || strpos($_REQUEST[$alias], 'katalog-proektov/homes') !== false || strpos($_REQUEST[$alias], 'homes/homes') !== false) {
                return;
            }


            if (strpos($_REQUEST[$alias], 'fasad/') !== false) {
                $url = $modx->makeUrl(1, '', '', 'full') . str_replace('fasad/', 'project-', $_REQUEST[$alias]);
                $modx->sendRedirect($url);
            }

            if (strpos($_REQUEST[$alias], 'krut/') !== false) {
                $url = $modx->makeUrl(1, '', '', 'full') . str_replace('krut/', 'project-', $_REQUEST[$alias]);
                $modx->sendRedirect($url);
            }

            if (strpos($_REQUEST[$alias], 'print/') !== false) {
                $url = $modx->makeUrl(1, '', '', 'full') . str_replace('print/', '', $_REQUEST[$alias]);
                $modx->sendRedirect($url);
            }

            /** @var SeoFilter $SeoFilter */
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                    $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            $pdo = $SeoFilter->pdo;
            if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) return false;

            $base_get = array_map('trim', explode(',',$SeoFilter->config['base_get']));
            $separator = $SeoFilter->config['separator'];
            $site_start = $SeoFilter->config['site_start'];
            $charset = $SeoFilter->config['charset'];
            $check = $novalue = $page = $fast_search = 0; //переменные для проверки
            $params = array(); //итоговый массив с параметром и значением
            $last_char = ''; //был ли в конце url-а слэш
            if (substr($_REQUEST[$alias], -1) == '/') {
                $last_char = '/';
            }
            $request = trim($_REQUEST[$alias], '/');
            $tmp = explode('/', $request);


            $q = $modx->newQuery('sfRule');
            $q->limit(0);
            $q->select(array('sfRule.*'));
            $page_ids = $page_aliases = array();
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $page_ids[$row['id']] = $row['page'];
                }
            }

            if(count($page_ids)) {
                $q = $modx->newQuery('modResource');
                $q->where(array('id:IN' => array_unique($page_ids)));
                $q->limit(0);
                $q->select('id,alias');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $page_aliases[$row['id']] = $row['alias'];
                    }
                }


                $r_tmp = array_reverse($tmp, 1);
                $tmp_id = 0;
                foreach ($r_tmp as $t_key => $t_alias) {
                    if ($page = array_search($t_alias, $page_aliases)) {
                        $tmp_id = $t_key;
                        break;
                    }
                }
                if ($page) {
                    for ($i = 0; $i <= $tmp_id; $i++) {
                        array_shift($tmp);
                    }
                } else {
                    if (in_array($site_start, $page_ids)) {
                        $page = $site_start;  //для тех у кого главная страница сайта - фильтр
                    }
                }
            }

            if($page) {
                if($url_array = $SeoFilter->findUrlArray(implode('/',$tmp),$page)) {
                    if($url_array['active']) {
                        $old_url = $url_array['old_url'];
                        $new_url = $url_array['new_url'];
                        $rule_id = $url_array['multi_id'];

                        if ($new_url && $new_url != implode('/', $tmp)) {
                            $url = $modx->makeUrl($page) . $new_url;
                            $modx->sendRedirect($url . $last_char);
                        }

                        $tmp = explode('/', $old_url);
                        $menutitle = '';
                        if ($url_array['menu_on']) {
                            $menutitle = $url_array['menutitle'];
                        }

                        $q = $modx->newQuery('sfUrlWord');
                        $q->sortby('priority','ASC');
                        $q->leftJoin('sfField','sfField','sfUrlWord.field_id = sfField.id');
                        $q->leftJoin('sfDictionary','sfDictionary','sfUrlWord.word_id = sfDictionary.id');
                        $q->where(array('sfUrlWord.url_id'=>$url_array['id']));
                        $q->select('sfUrlWord.id, sfField.id as field_id, sfField.alias as field_alias, sfDictionary.value as word_value, sfDictionary.input as word_input, sfDictionary.alias as word_alias');
                        if($q->prepare() && $q->stmt->execute()) {
                            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                if ($_GET[$row['field_alias']]) {
                                    $_GET[$row['field_alias']] .= ',' . $row['word_input'];
                                    $_REQUEST[$row['field_alias']] .= ',' . $row['word_input'];
                                    $params[$row['field_alias']] .= ',' . $row['word_input'];
                                } else {
                                    $_GET[$row['field_alias']] = $_REQUEST[$row['field_alias']] = $params[$row['field_alias']] = $row['word_input'];
                                }
                                if (!$menutitle) $menutitle = $row['word_value'];
                            }
                        }

                        $q = $modx->newQuery('sfFieldIds');
                        $q->where(array('multi_id'=>$rule_id));
                        $url_fields = $modx->getCount('sfFieldIds',$q);

                        if ((count($params) != $url_fields)) { //Доп проверка на изменения в базе
                            if($links = $pdo->getCollection('sfFieldIds', array('multi_id' => $rule_id), array('sortby' => 'priority'))) {
                                if (count($tmp) == count($links)) {  //дополнительная проверка на количество параметров в адресе и пересечении
                                    foreach ($links as $lkey => $link) {
                                        if ($field = $pdo->getArray('sfField', $link['field_id'])) {
                                            $alias = $field['alias'];
                                            if ($field['hideparam']) {
                                                if ($word = $pdo->getArray('sfDictionary', array('alias' => $tmp[$lkey]))) {
                                                    $_GET[$alias] = $_REQUEST[$alias] = $params[$alias] = $word['input'];
                                                    if (!$menutitle) $menutitle = $word['value'];
                                                }
                                            } else {
                                                $tmp_arr = explode($separator, $tmp[$lkey]);
                                                $word_alias = '';
                                                if ($field['valuefirst']) {
                                                    $del = array_pop($tmp_arr);
                                                    if ($del == $alias) {
                                                        $word_alias = implode($separator, $tmp_arr);
                                                    }
                                                } else {
                                                    $del = array_shift($tmp_arr);
                                                    if ($del == $alias) {
                                                        $word_alias = implode($separator, $tmp_arr);
                                                    }
                                                }
                                                if ($word_alias && $word = $pdo->getArray('sfDictionary', array('alias' => $word_alias, 'field_id' => $field['id']))) {
                                                    $_GET[$alias] = $_REQUEST[$alias] = $params[$alias] = $word['input'];
                                                    if (!$menutitle) $menutitle = $word['value'];
                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (count($params)) {
                            $original_params = array_diff_key(array_merge($params,$_GET),array_flip(array_merge(array($del_get),$base_get)));
                            $fast_search = true;
                            $SeoFilter->initialize($modx->context->key, array('page' => $page, 'params' => $params));
                            $params['servicePage'] = isset($_GET['page']) ? $_GET['page'] : 1;

                            $meta = $SeoFilter->getRuleMeta($params, $rule_id, $page, 0,0,$original_params);//, $_REQUEST[$alias]);
                            $meta['menutitle'] = $menutitle;

                            $meta['allProjects'] = '<span itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="[[++site_url]]homes" itemprop="url" rel="">Каталог проектов домов</a></span>';

                            if (!isset($meta['content'])) {
                                $meta['content'] = 'empty';
                            }

                            $modx->setPlaceholders($meta, 'sf.');
                            $category = isset($params['steni']) ? $params['steni'] : null;
                            $modx->setPlaceholder('sf.videos', $modx->runSnippet('getVideos', ['category' => $category]));
                            $modx->sendForward($page);
                        } else {
                            if ($url = $modx->getObject('sfUrls', array('page_id' => $page, 'old_url' => $old_url, 'multi_id' => $rule_id))) {
                                $url->set('active', 0);
                                $url->save();
                            }
                        }

                    } else {
                        $modx->setPlaceholder('sf.seo_id',$url_array['id']);
                    }
                }
            }
        }
        break;
}
