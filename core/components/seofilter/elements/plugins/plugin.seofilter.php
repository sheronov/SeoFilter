<?php
/** @var modX $modx */
switch ($modx->event->name) {
    case 'OnPageNotFound':
        $time = microtime(true);
        $alias = $modx->context->getOption('request_param_alias', 'q');
        if (isset($_REQUEST[$alias])) {

            /** @var SeoFilter $SeoFilter */
            $SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null, $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
            /** @var pdoTools $pdoTools */
           // $pdoTools = $modx->getService('pdoTools');

            if (!($SeoFilter instanceof SeoFilter)
            //    || !($pdoTools instanceof pdoTools)
            ) break;

            $last_char = '';
            if(substr($_REQUEST[$alias],-1) == '/') {
                $last_char = '/';
            }
            $request = $page = trim($_REQUEST[$alias], "/");


            $tmp = explode('/', $request);

            $separator = '-'; //по идее для каждого поля будет - сделать системной настройкой
            $valuefirst = 0; //значение спереди - сделать системной настройкой
            $redirect = 1; //перенаправлять на правильный приоритет - системная настройка

            $check = $novalue = 0; //значение найдено
            $page = 0; //страница, куда будем отправлять пользователя
            $fieldClass = 'sfField'; //базовый класс поля
            $site_start = $modx->context->getOption('site_start', 1);  //если назначена главной страница с фильтром
            $charset = $modx->context->getOption('	modx_charset', 'UTF-8');

            $params = array();


            $q = $modx->newQuery($fieldClass);
            $q->limit(0);
            $q->select(array('sfField.*'));
            $fields = array();
            $aliases = array();
            $pageids = array();
            if($q->prepare() && $q->stmt->execute()) {
                while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $pageids[$row['id']] = $row['page'];
                    $aliases[$row['id']] = $row['alias'];
                    $fields[$row['id']] = $row;
                }
            }


            if(count($tmp)) {
                $page_aliases = array();
                $q = $modx->newQuery('modResource');
                $q->where(array('id:IN'=>$pageids));
                $q->limit(0);
                $q->select('id,alias');
                if($q->prepare() && $q->stmt->execute()) {
                    while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $page_aliases[$row['id']] = $row['alias'];
                    }
                }
                $r_tmp = array_reverse($tmp,1);
                $tmp_id = 0;
                $search_bool = 0;
                foreach($r_tmp as $t_key => $t_alias) {
                    if($page = array_search($t_alias,$page_aliases)) {
                        $tmp_id = $t_key;
                        $search_bool = true;
                        break;
                    }
                }
                if($search_bool) {
                    for($i = 0; $i <= $tmp_id;$i++) {
                        $shift = array_shift($tmp);
                    }
                }


            }


            foreach($tmp as $tid => $tquery) {
                $query = explode($separator, $tquery);
                if(count($query) < 2) {
                    $novalue = true;
                    break;
                }
                if ($valuefirst) {
                    $param = array_pop($query);
                } else {
                    $param = array_shift($query);
                }
                $value = implode($separator, $query);
                $params[$param] = $value;
            }

            if(!$page && !$novalue) {
                if(in_array($site_start,$pageids)) {
                    $page = $site_start;  //для тех у кого главная страница сайта - фильтр
                }
            }

            $priorities = $priorities_asort = array();
            foreach($params as $param=>$value) {
                if ($field_id = array_search($param, $aliases)) {
                    $exact = $fields[$field_id]['exact'] || 1;
                    $class = $fields[$field_id]['class'];
                    $key = $fields[$field_id]['key'];
                    $priority = $fields[$field_id]['priority'];
                    $translit = $fields[$field_id]['translit'];

                    $priorities[$param] = $priorities_asort[$param] = $priority;

                    if($translit && $field = $modx->getObject($fieldClass,$field_id)) {
                        $value_tr = $field->translit($value);
                    }
                    $class_key = $class . '.' . $key;
                    $search_value = '';

                    $q = $modx->newQuery($class);
                    $q->limit(1);
                    if ($exact) {
                        $q->where(array($key . ':LIKE' => $value));
                        if(isset($value_tr)) {
                            $q->where(array('OR:'.$key . ':LIKE' => $value_tr));
                        }
                    } else {
                        $q->where(array($key . ':LIKE' => "%" . $value . "%"));
                        if(isset($value_tr)) {
                            $q->where(array('OR:'.$key . ':LIKE' => "%" .$value_tr. "%"));
                        }
                    }

                    $q->select(array('DISTINCT ' . $class_key));
                    if ($q->prepare() && $q->stmt->execute()) {
                        if(!$search_value = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                            $novalue = true;
                        }
                    }

                    if ($search_value && $param) {
                        $_GET[$param] = $_REQUEST[$param] = $search_value;
                        $check = true;
                    }
                }
            }

           // echo '<br>Time: '.(microtime(true) - $time).' s<br>';
           // echo 'Memory: '.round((memory_get_peak_usage(true) / 1024 / 1024),2).' Mb';
            if($check && $page && !$novalue) {
                if($redirect || $page==$site_start) {
                    $diff = 0;
                    $url_add = ''; //добавочный адрес для редиректа
                    asort($priorities_asort);
                    $priorities = array_values(array_flip($priorities));
                    $priorities_asort = array_values(array_flip($priorities_asort));
                    $add_urls = array();
                    foreach($priorities_asort as $p_key=>$p_param) {
                        if($p_param != $priorities[$p_key]) {
                            $diff = true;
                            $add_urls[] = mb_strtolower($p_param,$charset).$separator.mb_strtolower($params[$p_param],$charset);
                        }
                    }
                    if($diff) {
                        $url = $modx->makeUrl($page).implode('/',$add_urls);
                        $modx->sendRedirect($url.$last_char);
                    }
                }
                $modx->sendForward($page);

            }
        }
    break;
}
