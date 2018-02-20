<?php
/** @var modX $modx */
/** @var array $scriptProperties */
switch ($modx->event->name) {
    case 'OnLoadWebDocument':
        if($page = $modx->resource->id) {
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                    $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            if (!($SeoFilter instanceof SeoFilter)) break;
            $q = $modx->newQuery('sfRule');
            $q->where(array('page' => $page)); // Одно правило для одной страницы!
            if($modx->getCount('sfRule',$q)) {
                if(!$SeoFilter->initialized[$modx->resource->context_key]) {
                    $SeoFilter->initialize($modx->resource->context_keyy, array('page' => (int)$page));
                }
            }

            //TODO: сделать правильное подключение класса, чтобы без ошибок там где его нет
//            if($msVC = $modx->getService('msvendorcollections', 'msVendorCollections', $modx->getOption('msvendorcollections_core_path', null,
//                    $modx->getOption('core_path') . 'components/msvendorcollections/') . 'model/msvendorcollections/', $scriptProperties)) {
//                if (!$msVC->initialized[$modx->resource->context_key]) {
//                    $msVC->initialize($modx->resource->context_key);
//                }
//            }
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
//        $modx->log(modx::LOG_LEVEL_ERROR,'SeoFilter  = ' . print_r($r_array,1));
//        $modx->log(modx::LOG_LEVEL_ERROR,'SeoFilter DATA = ' . print_r($resource->Data->toArray(),1));
        $tv_names = array();
        if($tvs = $resource->getMany('TemplateVars')) {
            foreach($tvs as $tv) {
                $tv_names[$tv->get('name')] = $tv->get('id');
            }
        }

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
                        if(in_array($key,array_keys($tv_names))) {
                            $input = $resource->getTVValue($key);
                            //$modx->log(modx::LOG_LEVEL_ERROR,$field['name'] .' = ' . print_r($input,1));
                        }
                        break;
                    case 'msVendor':
                        $input = $r_array['vendor.id'];
                        break;
                    default:
                        break;
                }

                if(!is_array($input) && !is_numeric($input)) {
                    $result = json_decode($input);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $input = $result;
                    }
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
            /** @var SeoFilter $SeoFilter */
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
                    $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            $pdo = $SeoFilter->pdo;
            if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) break;

            $container_suffix = $SeoFilter->config['container_suffix'];
            $url_suffix = $SeoFilter->config['url_suffix'];
            $url_redirect = $SeoFilter->config['redirect'];

            $base_get = array_map('trim', explode(',',$SeoFilter->config['base_get']));
            $separator = $SeoFilter->config['separator'];
            $site_start = $SeoFilter->config['site_start'];
            $charset = $SeoFilter->config['charset'];
            $check = $novalue = $page = $fast_search = 0; //переменные для проверки
            $params = array(); //итоговый массив с параметром и значением
            $last_char = ''; //был ли в конце url-а слэш
//            if (substr($_REQUEST[$alias], -1) == '/') {
//                $last_char = '/';
//            }

            $request = trim($_REQUEST[$alias]);

            if ($url_suffix) {
                if (strpos($request, $url_suffix, strlen($request) - strlen($url_suffix))) {
                    $request = substr($request, 0, -strlen($url_suffix));
                    $last_char = $url_suffix; //был ли суффикс в конце

                }
            } elseif ($url_redirect) {
                if (substr($_REQUEST[$alias], -1) == '/') {
                    $last_char = '/';
                }
            }

            $request = trim($request, "/");

            $tmp = explode('/', $request);
            $r_tmp = array();

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
                $q->select('id,alias,uri_override,uri');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $uri = $row['alias'];
                        if($row['uri_override']) {
                            $uri = $row['uri'];
                        }

                        foreach($SeoFilter->config['possibleSuffixes'] as $possibleSuffix) {
                            if (substr($uri, -strlen($possibleSuffix)) == $possibleSuffix) {
                                $uri = substr($uri, 0, -strlen($possibleSuffix));
                            }
                        }

                        $page_aliases[$row['id']] = $uri;
                    }
                }



                $r_tmp = array_reverse($tmp, 1);
                $tmp_id = 0;

                foreach ($r_tmp as $t_key => $t_alias) {
                    $t_alias = trim($t_alias,'/');
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
                $p = $modx->newQuery('modResource',array('id'=>$page));
                $p->select('context_key');
                $ctx = $modx->getValue($p->prepare());
                if($page == $site_start) {
                    $url = '';
                } else {
                    $url = $modx->makeUrl($page,$ctx,'',-1);
                }
                if(strpos($url,$modx->getOption('site_url')) !== false) {
                    $url = str_replace($modx->getOption('site_url'),'',$url);
                }
                $c_suffix = $SeoFilter->config['container_suffix'];
                if($c_suffix) {
                    if(strpos($url,$c_suffix,strlen($url)-strlen($c_suffix))) {
                        $url = substr($url,0,-strlen($c_suffix));
                    }
                }
                foreach($SeoFilter->config['possibleSuffixes'] as $possibleSuffix) {
                    if (substr($url, -strlen($possibleSuffix)) == $possibleSuffix) {
                        $url = substr($url, 0, -strlen($possibleSuffix));
                    }
                }
                if(implode('/',array_reverse(array_diff($r_tmp,$tmp))) != trim($url,'/')) {
                    break;
                }
                if($tmp && $url_array = $SeoFilter->findUrlArray(implode('/',$tmp),$page)) {
                    if($url_array['active']) {
                        $old_url = $url_array['old_url'];
                        $new_url = $url_array['new_url'];
                        $rule_id = $url_array['multi_id'];

                        if ($new_url && ($new_url != implode('/', $tmp))) {
                            if ($container_suffix) {
                                if (strpos($url, $container_suffix, strlen($url) - strlen($container_suffix))) {
                                    $url = substr($url, 0, -strlen($container_suffix));
                                }
                            }
                            $modx->sendRedirect($url .'/'. $new_url . $url_suffix,false,'REDIRECT_HEADER','HTTP/1.1 301 Moved Permanently');
                        } elseif($url_redirect && ($url_suffix != $last_char)) {
                            if ($container_suffix) {
                                if (strpos($url, $container_suffix, strlen($url) - strlen($container_suffix))) {
                                    $url = substr($url, 0, -strlen($container_suffix));
                                }
                            }
                            $modx->sendRedirect($url .'/'. implode('/', $tmp) . $url_suffix,false,'REDIRECT_HEADER','HTTP/1.1 301 Moved Permanently');
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
                                $_GET[$row['field_alias']] = $_REQUEST[$row['field_alias']] = $params[$row['field_alias']] = $row['word_input'];
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
                            $meta = $SeoFilter->getRuleMeta($params, $rule_id, $page, 0,0,$original_params);
                            if($SeoFilter->config['hideEmpty'] && $SeoFilter->config['count_childrens'] && !$meta['total']) {
                                $modx->setPlaceholder('sf.seo_id',$url_array['id']);
                                break;
                            }
                            $SeoFilter->initialize($ctx, array('page' => $page, 'params' => $params));
                            $meta['menutitle'] = $menutitle;
                            if(isset($meta['properties'])) {
                                $meta['properties'] = $modx->toJSON($meta['properties']);
                            }
                            if(isset($meta['introtexts'])) {
                                $meta['introtexts'] = $modx->toJSON($meta['introtexts']);
                            }
                            if($ctx != 'web') {
                                $modx->switchContext($ctx);
                            }
                            $modx->setPlaceholders($meta, 'sf.');
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