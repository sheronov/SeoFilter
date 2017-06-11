<?php
/** @var modX $modx */
switch ($modx->event->name) {
    case 'OnLoadWebDocument':
        if($page = $modx->resource->id) {
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null, $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            if (!($SeoFilter instanceof SeoFilter)) break;
            $q = $modx->newQuery('sfField');
            $q->where(array('page' => $page));
            if($modx->getCount('sfField',$q)) {
                if(!$SeoFilter->initialized[$modx->context->key]) {
                    $SeoFilter->initialize($modx->context->key, array('page' => (int)$page));
                }
            }
        }
        break;
    case 'OnPageNotFound':
        $time = microtime(true);
        $alias = $modx->context->getOption('request_param_alias', 'q');
        if (isset($_REQUEST[$alias])) {
            /** @var SeoFilter $SeoFilter */
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null, $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            $pdo = $SeoFilter->pdo;
            if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) break;
            $separator = $SeoFilter->config['separator'];
            $site_start = $SeoFilter->config['site_start'];
            $charset = $SeoFilter->config['charset'];
            $check = $novalue = $page = $fast_search = 0; //переменные для проверки
            $params = array(); //итоговый массив с параметром и значением
            $last_char = ''; //был ли в конце url-а слэш

            if (substr($_REQUEST[$alias], -1) == '/') {
                $last_char = '/';
            }
            $request = trim($_REQUEST[$alias], "/");
            $tmp = explode('/', $request);

            $q = $modx->newQuery('sfField');
            $q->limit(0);
            $q->select(array('sfField.*'));
            $fields = $aliases = $pageids = $url_parts = $findparams = array();
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $pageids[$row['id']] = $row['page'];
                    $aliases[$row['id']] = $row['alias'];
                    $fields[$row['id']] = $row;
                }
            }

            if (count($tmp)) {
                $page_aliases = array();
                $q = $modx->newQuery('modResource');
                $q->where(array('id:IN' => $pageids));
                $q->limit(0);
                $q->select('id,alias');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $page_aliases[$row['id']] = $row['alias'];
                    }
                }

                $r_tmp = array_reverse($tmp, 1);
                $tmp_id = 0;
                $search_bool = 0;
                foreach ($r_tmp as $t_key => $t_alias) {
                    if ($page = array_search($t_alias, $page_aliases)) {
                        $tmp_id = $t_key;
                        $search_bool = true;
                        break;
                    }
                }
                if ($search_bool) {
                    for ($i = 0; $i <= $tmp_id; $i++) {
                        $url_parts[] = array_shift($tmp);
                    }
                }
            }


            if (count($tmp) == 1 && $page) {
                if ($word = $pdo->getArray('sfDictionary', array('alias' => $tmp[0]))) {
                    if ($field = $pdo->getArray('sfField', $word['field_id'])) {
                        $alias = $field['alias'];
                        $_GET[$alias] = $_REQUEST[$alias] = $findparams[$alias] = $word['input'];
                        $fast_search = true;
                        $SeoFilter->initialize($modx->context->key,array('page'=>$page,'params'=>$findparams));
                        $meta = $SeoFilter->getFieldMeta($findparams);
                        $modx->setPlaceholders($meta,'sf.');
                        $modx->sendForward($page);
                    }
                }
            }

            if(count($tmp) == 1 && $url_array = $SeoFilter->findUrlArray($tmp[0])) {
                $old_url = $url_array['old_url'];
                $tmp = explode('/',$old_url);
            }

                $find_url = implode("/",$tmp);
                if($find_multi = $SeoFilter->findUrlArray($find_url)['multi_id']) {
                    if($links = $pdo->getCollection('sfFieldIds',array('multi_id'=>$find_multi),array('sortby' => 'priority'))) {
                        if(count($tmp) == count($links)) {  //дополнительная проверка на количество параметров в адресе и пересечении
                            foreach($links as $lkey => $link) {
                                $field = $pdo->getArray('sfField',$link['field_id']);
                                $alias = $field['alias'];
                                if($field['hideparam']) {
                                    if ($word = $pdo->getArray('sfDictionary', array('alias' => $tmp[$lkey]))) {
                                        $_GET[$alias] = $_REQUEST[$alias] = $findparams[$alias] = $word['input'];
                                    }
                                } else {
                                    $tmp_arr = explode($separator,$tmp[$lkey]);
                                    $walias = '';
                                    if($field['valuefirst']) {
                                        $del = array_pop($tmp_arr);
                                        if($del == $alias) {
                                            $walias = implode($separator,$tmp_arr);
                                        }
                                    } else {
                                        $del = array_shift($tmp_arr);
                                        if($del == $alias) {
                                            $walias = implode($separator,$tmp_arr);
                                        }
                                    }
                                    if($walias && $word = $pdo->getArray('sfDictionary', array('alias' => $walias))) {
                                        $_GET[$alias] = $_REQUEST[$alias] = $findparams[$alias] = $word['input'];
                                    }
                                }
                            }
                            $fast_search = true;
                            $SeoFilter->initialize($modx->context->key,array('page'=>$page,'params'=>$findparams));
                            $meta = $SeoFilter->getMultiMeta($findparams,$page);
                            $modx->setPlaceholders($meta,'sf.');
                            $modx->sendForward($page);
                        } else {
                            echo 'данное исключение пока не обработано. Для кастомных урл'; //TODO: Обработать
                            die;
                        }
                    }
                }




                foreach ($tmp as $tid => $tquery) {
                    $query = explode($separator, $tquery);
                    if (count($query) < 2) {
                        $novalue = true;
                        break;
                    }
                    if ($SeoFilter->config['valuefirst']) {
                        $param = array_pop($query);
                    } else {
                        $param = array_shift($query);
                    }
                    $value = implode($separator, $query);
                    $params[$param] = $value;
                }

                if (!$page && !$novalue) {
                    if (in_array($site_start, $pageids)) {
                        $page = $site_start;  //для тех у кого главная страница сайта - фильтр
                    }
                }

                $priorities = $priorities_asort = array();
                foreach ($params as $param => $value) {
                    if ($field_id = array_search($param, $aliases)) {
                        $exact = $fields[$field_id]['exact'] || 1;
                        $class = $fields[$field_id]['class'];
                        $key = $fields[$field_id]['key'];
                        $priority = $fields[$field_id]['priority'];
                        $translit = $fields[$field_id]['translit'];

                        $priorities[$param] = $priorities_asort[$param] = $priority;

                        if ($translit && $field = $modx->getObject('sfField', $field_id)) {
                            $value_tr = $field->translit($value);
                        }
                        $class_key = $class . '.' . $key;
                        $search_value = '';

                        $q = $modx->newQuery($class);
                        $q->limit(1);
                        if ($exact) {
                            $q->where(array($key . ':LIKE' => $value));
                            if (isset($value_tr)) {
                                $q->where(array('OR:' . $key . ':LIKE' => $value_tr));
                            }
                        } else {
                            $q->where(array($key . ':LIKE' => "%" . $value . "%"));
                            if (isset($value_tr)) {
                                $q->where(array('OR:' . $key . ':LIKE' => "%" . $value_tr . "%"));
                            }
                        }

                        $q->select(array('DISTINCT ' . $class_key));
                        if ($q->prepare() && $q->stmt->execute()) {
                            if (!$search_value = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                                $novalue = true;
                            }
                        }

                        if ($search_value && $param) {
                            $_GET[$param] = $_REQUEST[$param] = $findparams[$param] = $search_value;
                            $check = true;
                        }
                    }
                }

                // echo '<br>Time: '.(microtime(true) - $time).' s<br>';
                // echo 'Memory: '.round((memory_get_peak_usage(true) / 1024 / 1024),2).' Mb';
                if ($check && $page && !$novalue) {
                    if ($SeoFilter->config['redirect'] || $page == $site_start) {
                        $diff = 0;
                        $url_add = ''; //добавочный адрес для редиректа
                        asort($priorities_asort);
                        $priorities = array_values(array_flip($priorities));
                        $priorities_asort = array_values(array_flip($priorities_asort));
                        $add_urls = array();
                        foreach ($priorities_asort as $p_key => $p_param) {
                            if ($p_param != $priorities[$p_key]) {
                                $diff = true;
                                $add_urls[] = mb_strtolower($p_param, $charset) . $separator . mb_strtolower($params[$p_param], $charset);
                            }
                        }
                        if ($diff) {
                            $url = $modx->makeUrl($page) . implode('/', $add_urls);
                            $modx->sendRedirect($url . $last_char);
                        }
                    }
                    $SeoFilter->initialize($modx->context->key,array('page'=>$page,'params'=>$findparams));
                    $meta = $SeoFilter->getFieldMeta($findparams);
                    $modx->setPlaceholders($meta,'sf.');
                    $modx->sendForward($page);
                }
            }
    break;
}
