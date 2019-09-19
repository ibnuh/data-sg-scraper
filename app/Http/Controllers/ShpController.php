<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use voku\helper\HtmlDomParser;
use \Curl\Curl;

class ShpController extends Controller
{
    public function getDownloadLink($url = 'https://data.gov.sg/dataset/master-plan-2014-planning-area-boundary-web'){
        $curl = new Curl();
        $curl->get($url);
        
        if($curl->error){
            echo $curl->errorMessage;
            return;
        }

        $dom = HtmlDomParser::str_get_html($curl->response);
        $downloadButton = $dom->findOneOrFalse('.ga-dataset-download');

        if(!$downloadButton) return false;

        $downloadUrl = "https://data.gov.sg" . $downloadButton->href;

        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->get($downloadUrl);
        return $curl->effectiveUrl;
    }

    public function downloadFile($url){
        $curl = new Curl();
        $filename = basename(parse_url($url, PHP_URL_PATH));
        
        echo "Downloading: $filename" . PHP_EOL;

        $curl->download($url, storage_path('app/public/raw/' . $filename));

        echo "Download completed" . PHP_EOL;
    }

    public function scrape(){
        $urls = [
            "https://data.gov.sg/dataset/parks",
            "https://data.gov.sg/dataset/residents-committees",
            "https://data.gov.sg/dataset/park-facilities",
            "https://data.gov.sg/dataset/master-plan-2014-gross-plot-ratio-line",
            "https://data.gov.sg/dataset/sdcp-activity-1st-storey",
            "https://data.gov.sg/dataset/mp08-rail-line",
            "https://data.gov.sg/dataset/sdcp-promenade",
            "https://data.gov.sg/dataset/sdcp-park-connector",
            "https://data.gov.sg/dataset/sdcp-park",
            "https://data.gov.sg/dataset/master-plan-2014-underground-line",
            "https://data.gov.sg/dataset/mp14-sdcp-sbud-plan-urban-design-area",
            "https://data.gov.sg/dataset/master-plan-2014-rail-station",
            "https://data.gov.sg/dataset/mp14-sdcp-pw-plan-waterbody",
            "https://data.gov.sg/dataset/mp14-sdcp-pw-plan-park-connector",
            "https://data.gov.sg/dataset/mp14-sdcp-pw-plan-park-connector-line",
            "https://data.gov.sg/dataset/mp14-sdcp-pw-plan-parks-and-open-space",
            "https://data.gov.sg/dataset/mp14-sdcp-pw-plan-nature-boundary-line-offset",
            "https://data.gov.sg/dataset/mp14-sdcp-pw-plan-mall-and-promenade",
            "https://data.gov.sg/dataset/mp14-sdcp-bh-plan-building-height-control-plan-no-of-storeys",
            "https://data.gov.sg/dataset/mp14-sdcp-agu-plan-basement-mandatory",
            "https://data.gov.sg/dataset/mp14-sdcp-agu-plan-1st-storey-mandatory",
            "https://data.gov.sg/dataset/sdcp-urban-design-guidelines",
            "https://data.gov.sg/dataset/list-of-verified-public-access-aed-locations",
            "https://data.gov.sg/dataset/sdcp-landed-housing-area",
            "https://data.gov.sg/dataset/sdcp-conservation-area-boundary",
            "https://data.gov.sg/dataset/sdcp-building-height-control-plan-storey",
            "https://data.gov.sg/dataset/street-and-places",
            "https://data.gov.sg/dataset/pcn-access-points",
            "https://data.gov.sg/dataset/voluntary-welfare-orgs",
            "https://data.gov.sg/dataset/conservation-area-map",
            "https://data.gov.sg/dataset/master-plan-2014-planning-area-boundary-web",
            "https://data.gov.sg/dataset/community-in-bloom-cib",
            "https://data.gov.sg/dataset/malaria-receptive-areas",
            "https://data.gov.sg/dataset/skyrise-greenery",
        ];

        foreach($urls as $url){
            echo "------------------------------------------------------------" . PHP_EOL;
            echo "Processing: $url" . PHP_EOL;

            $downloadLink = $this->getDownloadLink($url);
            $this->downloadFile($downloadLink);
        }
    }

    public function moveShpZipFile(){
        $directories = glob('/Users/ibnuh/work/datasg/storage/app/public/extracted/*' , GLOB_ONLYDIR);

        foreach($directories as $directory){
            $files = glob($directory . '/*');
            foreach($files as $file){
                if(strpos($file, 'shp.zip') !== false){
                    shell_exec("mv $file /Users/ibnuh/work/datasg/storage/app/public/shp-zip");
                }
            }
        }
    }

    public function moveShpFile(){

    }
}
