<?php
use Sunra\PhpSimple\HtmlDomParser;

class Parser_Farpost extends ParserItem
{
	protected $SiteUrl = 'http://www.farpost.ru';

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

		// TODO РЕГИОН!!!
		for($i = 1; $i <= $this->MaxPagesDepth; $i++)
		{
			$curlClient->add([ CURLOPT_URL => Settings::getValue('proxy-prefix') . $this->SiteUrl .
				'/primorskii-krai/auto/sale/+/' . $this->getCarMark()->title . '+' .
				$this->getCarModel()->title /* . '/' . 'page' . $i . '/' */ ]);
		}

		$carsListResult = $curlClient->all();

		$retData = array();

		foreach($carsListResult as $listResult)
		{
			$pageContent = HtmlDomParser::str_get_html($listResult->getBody());
			$rows = $pageContent->find('.viewdirBulletinTable > .native > tr');

			$pageIndex = 1;

			foreach($rows as $carRow)
			{
				$advertData = [ ];

				$imageCell = $carRow->find('.imageCell', 0);
				$descriptionCell = $carRow->find('.descriptionCell', 0);
				$dateCell = $carRow->find('.dateCell', 0);

				// Когда начинается блок доставки - останавливаемся.
				if(!empty($carRow->find('#statusdelivery')))
				{
					break;
				}

				if(!empty($imageCell))
				{
					$imageElem = $imageCell->find('meta[itemprop="image"]', 0);
					$annotation = $descriptionCell->find('.annotation', 0)->plaintext;

					$carFeatures = '';
					if(preg_match('#.*\d\.\d.*#i', $annotation))
					{
						$carFeatures = preg_replace('#.*(\d\.\d).*#i', '$1 л', $annotation);
					}

					if(strpos($annotation, 'бензин') !== false)
					{
						$carFeatures .= ', бензин';
					}
					elseif(strpos($annotation, 'дизель') !== false)
					{
						$carFeatures .= ', дизель';
					}

					if(strpos($annotation, 'автомат') !== false)
					{
						$carFeatures .= ', автомат';
					}
					elseif(strpos($annotation, 'коробка') !== false)
					{
						$carFeatures .= ', механика';
					}

					if(strpos($annotation, '4wd') !== false)
					{
						$carFeatures .= ', 4WD';
					}

					$descProp = $descriptionCell->find('meta[itemprop="description"]', 0);

					$carYear = '';

					if(!empty($descProp))
					{
						$carYear = intval(preg_replace('#.*?(\d{4})#i', '$1', $descProp->getAttribute('content')));
					}
					else
					{
						$billLink = $descriptionCell->find('.bulletinLink', 0);

						if(!empty($billLink))
						{
							$carYear = intval(preg_replace('#.*?(\d{4})#i', '$1', $billLink->plaintext));
						}
					}

					if(empty($carYear))
					{
						continue;
					}

					$priceItem = $descriptionCell->find('meta[itemprop="price"]', 0);

					if(empty($priceItem))
					{
						continue;
					}

					$advertData['parser_id'] = $this->getParserId();
					$advertData['internal_id'] = intval($imageCell->getAttribute('data-bulletin-id'));
					$advertData['image_url'] =  (!empty($imageElem) ? preg_replace('#(.*?)_default$#i', '$1_thumbnail120', $imageElem->getAttribute('content')) : '');
					$advertData['car_mark'] = $this->CarMark;
					$advertData['car_model'] = $this->CarModel;
					$advertData['car_year'] = $carYear;
					$advertData['car_features'] = trim($carFeatures, ', ');
					$advertData['car_mileage'] = 0;
					$advertData['price'] = intval($priceItem->getAttribute('content'));
					$advertData['city'] = trim($dateCell->find('.city', 0)->plaintext);
					$advertData['is_sold'] = 0;
					$advertData['nodocs'] = intval(strpos($annotation, 'без птс') !== false);
					$advertData['damaged'] = 0;
					$advertData['page_ord'] = $pageIndex++;
					$advertData['content_raw'] = '';
					$advertData['date'] = preg_replace('#.*?\:(\d{4})(\d{2})(\d{2}).*#i', '$1-$2-$3', $imageCell->getAttribute('data-order-key'));

					$retData['car_' . $advertData['internal_id']] = $advertData;
				}
			}
		}

		return $retData;
	}

	public function getModelsData()
    {
        // TODO: Implement getModelsData() method.
    }
}
