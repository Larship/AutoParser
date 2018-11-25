<?php
use Sunra\PhpSimple\HtmlDomParser;

class Parser_Drom extends ParserItem
{
	protected $SiteUrl = 'http://auto.drom.ru';

	public function getCarsData()
	{
		$curlOptions = [
			CURLOPT_HTTPGET => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 5,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FRESH_CONNECT => true,
		];

		$curlClient = new MCurl_Client();
		$curlClient->setCurlOption($curlOptions);
		$curlClient->setMaxRequest(30);

		for($i = 1; $i <= $this->MaxPagesDepth; $i++)
		{
			$curlClient->add([ CURLOPT_URL => Settings::getValue('proxy-prefix') . $this->SiteUrl . '/' . $this->getRegion() . '/' . $this->getCarMark()->url . '/' . $this->getCarModel()->url . '/' . 'page' . $i . '/' ]);
		}

		$carsListResult = $curlClient->all();

		$retData = array();

		foreach($carsListResult as $listResult)
		{
			$pageContent = HtmlDomParser::str_get_html($listResult->getBody());

			$rows = $pageContent->find('.b-media-cont > a.b-advItem');

			foreach($rows as $carRow)
			{
				$advertData = [ ];

				$imageUrl = $carRow->find('.b-advItem__inner > .b-advItem__pic img', 0)->getAttribute('src');

				$advertData['parser_id'] = $this->getParserId();
				$advertData['internal_id'] = intval(preg_replace('#.*\/(.*)\.html$#i', '$1', $carRow->getAttribute('href')));
				$advertData['image_url'] = $imageUrl;
				$advertData['car_mark'] = $this->CarMark;
				$advertData['car_model'] = $this->CarModel;
				$advertData['car_year'] = intval(preg_replace('#.*\,\s(\d)#i', '$1', $carRow->find('.b-advItem__section_type_main > .b-advItem__title', 0)->plaintext));
				$advertData['car_mileage'] = intval(preg_replace('#\D#', '', $carRow->find('.b-advItem__section_type_params > .b-advItem__params > .b-advItem__param', -1)->plaintext));
				$advertData['price'] = intval(preg_replace('#\D#', '', $carRow->find('.b-advItem__section_type_price > .b-advItem__price', 0)->plaintext));
				$advertData['city'] = trim($carRow->find('.b-advItem__section_type_price > .b-advItem__params > .b-advItem__param', 0)->plaintext);
				$advertData['is_sold'] = intval(strpos($carRow->class, 'b-advItem_removed') !== false);
				$advertData['nodocs'] = intval(!empty($carRow->find('.b-advItem__section_type_params > .b-advItem__status > .b-advItem__icon_type_nodocs', 0)));
				$advertData['damaged'] = intval(!empty($carRow->find('.b-advItem__section_type_params > .b-advItem__status > .b-advItem__icon_type_hummer', 0)));
				$advertData['page_ord'] = intval(($i - 1) * 20) + intval($carRow->getAttribute('name'));
				$advertData['content_raw'] = '';

				$featuresRows = $carRow->find('.b-advItem__section_type_params > .b-advItem__params > .b-advItem__param');
				$featuresText = '';
				foreach($featuresRows as $feature)
				{
					$featureTextSingle = trim($feature->plaintext, ', ');

					// Километраж не нужно сохранять
					if (strpos($featureTextSingle, 'тыс.&nbsp;км') === false)
					{
						$featuresText .= $featureTextSingle . ', ';
					}
				}

				$advertData['car_features'] = trim($featuresText, ', ');

				// Определяем дату публикации объявления
				$advertDate = $carRow->find('.b-advItem__section_type_price > .b-advItem__params > .b-advItem__param', -1)->plaintext;
				$advertDate = explode(' ', str_replace('&nbsp;', ' ', $advertDate)); // У них там ставится &nbsp; вместо первого пробела
				$monthIndex = Dates::monthNameGenIndex($advertDate[1]);
				$advertDateStr = date('Y') . '-' . ($monthIndex ? $monthIndex : date('n')) . '-' . ($monthIndex ? $advertDate[0] : date('d'));
				$advertDatetime = new DateTime($advertDateStr);

				// Фикс бага, когда при парсинге в конце декабря 2015 года дата у объявлений была 2016-12-31 (неправильно ставился год) -->
				// 03.02.2016
				$datetimeDiff = (new DateTime())->diff($advertDatetime, true);

				if($datetimeDiff->days > 170)
				{
					$advertDatetime->sub(new DateInterval('P1Y'));
				}
				// <--

				$advertData['date'] = $advertDatetime->format('Y-m-d');

				$retData['car_' . $advertData['internal_id']] = $advertData;
			}
		}

		$curlClient = new MCurl_Client();
		$curlClient->setCurlOption($curlOptions);
		$curlClient->setMaxRequest(30);

		foreach($retData as $retItem)
		{
			$curlClient->add([ CURLOPT_URL => $retItem['internal_id'] . '.drom.ru' ], [ 'internal_id' => $retItem['internal_id'] ]);
		}

		$carInfoResults = $curlClient->all();

		foreach($carInfoResults as $infoResult)
		{
			$infoBody = iconv('Windows-1251', 'UTF-8', $infoResult->getBody());

			$startStr = '<span class="b-text-gray">Дополнительно:</span>';
			$startPos = strpos($infoBody, $startStr);

			if($startPos !== false)
			{
				$endPos = strpos($infoBody, '<span class="b-text-gray"><svg', $startPos + strlen($startStr));

				$content = substr($infoBody, $startPos, $endPos - $startPos);
				$content = preg_replace('#</p>\s*<p>#im', '', $content);
				$content = trim($content);

				$retData['car_' . $infoResult->getParams()['internal_id']]['content_raw'] = $content;
			}
		}

		return $retData;
	}

	public function getModelsData()
    {
        $curlOptions = [
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FRESH_CONNECT => true,
        ];

        $curlClient = new MCurl_Client();
        $curlClient->setCurlOption($curlOptions);
        $curlClient->setMaxRequest(30);
        $curlClient->add([ CURLOPT_URL => Settings::getValue('proxy-prefix') . $this->SiteUrl . '/' . $this->getCarMark()->url . '/' ]);

        $carsListResult = $curlClient->all();

        $retData = [];

        foreach($carsListResult as $listResult)
        {
            $pageContent = HtmlDomParser::str_get_html($listResult->getBody());

            $models = $pageContent->find('.b-selectCars__section .b-selectCars__item a');

            foreach($models as $modelElement)
            {
                // На разных сайтах могут быть разные URL'ы, поэтому для каждого сайта нужно реализовывать свой метод
                $retData[] = [
                    'mark_id' => $this->getCarMark()->id,
                    'title' => $modelElement->plaintext,
                    'url' => preg_replace('#.*\/(.*)\/$#i', '$1', $modelElement->getAttribute('href')),
                ];
            }
        }

        return $retData;
    }
}
