<?php

class sfDictionary extends xPDOSimpleObject
{


    public function save($cacheFlag = null)
    {
        $update = (int)$this->get('update');
        $decline = $this->xpdo->getOption('seofilter_decline', null, 0, true);
        if ($decline && $word = $this->get('value')) {
            if ($word && !$this->get('value_i')) {
                $this->set('value_i', $word);
            }
            if ($value = $this->morpher($word)) {
                if (!empty($value['Р']) && (!$this->get('value_r') || $update)) {
                    $this->set('value_r', $value['Р']);
                }
                if (!empty($value['Д']) && (!$this->get('value_d') || $update)) {
                    $this->set('value_d', $value['Д']);
                }
                if (!empty($value['В']) && (!$this->get('value_v') || $update)) {
                    $this->set('value_v', $value['В']);
                }
                if (!empty($value['Т']) && (!$this->get('value_t') || $update)) {
                    $this->set('value_t', $value['Т']);
                }
                if (!empty($value['П']) && (!$this->get('value_p') || $update)) {
                    $this->set('value_p', $value['П']);
                }
                if (!empty($value['П-о']) && (!$this->get('value_o') || $update)) {
                    $this->set('value_o', $value['П-о']);
                }
                if (!empty($value['где']) && (!$this->get('value_in') || $update)) {
                    $this->set('value_in', $value['где']);
                }
                if (!empty($value['куда']) && (!$this->get('value_to') || $update)) {
                    $this->set('value_to', $value['куда']);
                }
                if (!empty($value['откуда']) && (!$this->get('value_from') || $update)) {
                    $this->set('value_from', $value['откуда']);
                }
                if (!empty($value['множественное']) && $values = (array)$value['множественное']) {
                    if (!empty($values['И']) && (!$this->get('m_value_i') || $update)) {
                        $this->set('m_value_i', $values['И']);
                    }
                    if (!empty($values['Р']) && (!$this->get('m_value_r') || $update)) {
                        $this->set('m_value_r', $values['Р']);
                    }
                    if (!empty($values['Д']) && (!$this->get('m_value_d') || $update)) {
                        $this->set('m_value_d', $values['Д']);
                    }
                    if (!empty($values['В']) && (!$this->get('m_value_v') || $update)) {
                        $this->set('m_value_v', $values['В']);
                    }
                    if (!empty($values['Т']) && (!$this->get('m_value_t') || $update)) {
                        $this->set('m_value_t', $values['Т']);
                    }
                    if (!empty($values['П']) && (!$this->get('m_value_p') || $update)) {
                        $this->set('m_value_p', $values['П']);
                    }
                    if (!empty($values['П-о']) && (!$this->get('m_value_o') || $update)) {
                        $this->set('m_value_o', $values['П-о']);
                    }
                }
            }
        }
        $this->set('editedon', strtotime(date('Y-m-d H:i:s')));
        return parent::save($cacheFlag);
    }

    public function morpher($text = '')
    {
        $token = $this->xpdo->getOption('seofilter_morpher_token', null, '', true);
        $url = 'https://ws3.morpher.ru/russian/declension?format=json&s=';
        $urlGet = $url.urlencode($text);
        if ($token) {
            $urlGet .= '&token='.$token;
        }
        $out = '';
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $urlGet);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_USERAGENT,
                "Mozilla/5.0(Windows;U;WindowsNT5.1;ru;rv:1.9.0.4)Gecko/2008102920AdCentriaIM/1.7Firefox/3.0.4");
            $out = curl_exec($curl);
        }
        $response = $this->xpdo->fromJSON($out);
        if (!empty($response['code']) && !empty($response['message'])) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,
                '[SeoFilter] Morpher error: Code: '.$response['code'].'. Message: '.$response['message']);
            return null;
        }
        return $response;
    }


}