<?php

if (!class_exists('pdoPage')) {
    require_once MODX_CORE_PATH . 'components/pdotools/model/pdotools/pdopage.class.php';
}

class seoPage extends pdoPage {
    public $params = array();

    public function __construct(modX $modx, array $config = array())
    {
        if(!empty($modx->getPlaceholder('sf.params'))) {
            $this->params = $modx->fromJSON($modx->getPlaceholder('sf.params'));
        }


        parent::__construct($modx, $config);
    }

    public function makePageLink($url = '', $page = 1, $tpl = '')
    {
        if (empty($url)) {
            $url = $this->getBaseUrl();
        }

        $link = $pcre = '';
        if (!empty($this->pdoTools->config['pageLinkScheme'])) {
            $pls = $this->pdoTools->makePlaceholders(array(
                'pageVarKey' => $this->pdoTools->config['pageVarKey'],
                'page' => $page,
            ));
            $link = str_replace($pls['pl'], $pls['vl'], $this->pdoTools->config['pageLinkScheme']);
            $pcre = preg_replace('#\d+#', '(\d+)', preg_quote(preg_replace('#\#.*#', '', $link)));
        }

        $href = !empty($link)
            ? preg_replace('#' . $pcre . '#', '', $url)
            : $url;
        if ($page > 1 || ($page == 1 && !empty($this->pdoTools->config['ajax']))) {
            if (!empty($link)) {
                $href .= $link;
            } else {
                $href .= strpos($href, '?') !== false
                    ? '&'
                    : '?';
                $href .= $this->pdoTools->config['pageVarKey'] . '=' . $page;
            }
        }

        $GET = array_diff_key($_GET,$this->params);
        // $this->modx->log(1,print_r($this->params,1));
        // $this->modx->log(1,print_r($_GET,1));
        // $GET = $_GET;

        if (!empty($GET)) {
            $request = $GET;
            array_walk_recursive($request, function(&$item) {
                $item = rawurldecode($item);
            });
            unset($request[$this->req_var]);
            unset($request[$this->pdoTools->config['pageVarKey']]);

            if (!empty($request)) {
                $href .= strpos($href, '?') !== false
                    ? '&'
                    : '?';
                $href .= http_build_query($request);
            }
        }

        if (!empty($href) && $this->modx->getOption('xhtml_urls', null, false)) {
            $href = preg_replace("/&(?!amp;)/", "&amp;", $href);
        }

        $data = array(
            'page' => $page,
            'pageNo' => $page,
            'href' => $href,
        );

        return !empty($tpl)
            ? $this->pdoTools->getChunk($tpl, $data)
            : $href;
    }
}