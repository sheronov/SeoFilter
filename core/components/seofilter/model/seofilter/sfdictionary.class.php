<?php
class sfDictionary extends xPDOSimpleObject {

    public function save($cacheFlag = null) {
        $decline = $this->xpdo->getOption('seofilter_decline', null, 0, true);
        if(($word = $this->get('value')) && $decline) {
            if($word && !$this->get('value_i'))
                $this->set('value_i',$word);
            if ($value = $this->morpher($word)) {
                if($value['Р'] && !$this->get('value_r'))
                    $this->set('value_r',$value['Р']);
                if ($value['Д'] && !$this->get('value_d'))
                    $this->set('value_d', $value['Д']);
                if ($value['В'] && !$this->get('value_v'))
                    $this->set('value_v', $value['В']);
                if ($value['Т'] && !$this->get('value_t'))
                    $this->set('value_t', $value['Т']);
                if ($value['П'] && !$this->get('value_p'))
                    $this->set('value_p', $value['П']);
                if ($value['П-о'] && !$this->get('value_o'))
                    $this->set('value_o', $value['П-о']);
                if ($value['где'] && !$this->get('value_in'))
                    $this->set('value_in', $value['где']);
                if ($value['куда'] && !$this->get('value_to'))
                    $this->set('value_to', $value['куда']);
                if ($value['откуда'] && !$this->get('value_from'))
                    $this->set('value_from', $value['откуда']);
                if($value['множественное']) {
                    if ($values = (array)$value['множественное']) {
                        if ($values['И'] && !$this->get('values_i'))
                            $this->set('values_i', $values['И']);
                        if ($values['Р'] && !$this->get('values_r'))
                            $this->set('values_r', $values['Р']);
                        if ($values['Д'] && !$this->get('values_d'))
                            $this->set('values_d', $values['Д']);
                        if ($values['В'] && !$this->get('values_v'))
                            $this->set('values_v', $values['В']);
                        if ($values['Т'] && !$this->get('values_t'))
                            $this->set('values_t', $values['Т']);
                        if ($values['П'] && !$this->get('values_p'))
                            $this->set('values_p', $values['П']);
                        if ($values['П-о'] && !$this->get('values_o'))
                            $this->set('values_o', $values['П-о']);
                    }
                }
            }
        }
        return parent::save($cacheFlag);
    }

    public function morpher($text)
    {
        $username = $this->xpdo->getOption('seofilter_morpher_username', null, '', true);
        $password = $this->xpdo->getOption('seofilter_morpher_password', null, '', true);
        $credentials = array();
        if($username) {
            $credentials['Username'] = $username;
        }
        if($password) {
            $credentials['Password'] = $password;
        }
        $header = new SOAPHeader('http://morpher.ru/',
            'Credentials', $credentials);
        $url = 'http://morpher.ru/WebService.asmx?WSDL';
        try {
            $client = new SoapClient($url);
            $client->__setSoapHeaders($header);

            $params = array('parameters'=>array('s'=>$text));
            $result = (array) $client->__soapCall('GetXml', $params);
        }
        catch(Exception $e)
        {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'SeoFilter: "'.$text.'" - '. $e->faultstring);
            return array();
        }
        $singular = (array) $result['GetXmlResult'];
        return $singular;
    }

}