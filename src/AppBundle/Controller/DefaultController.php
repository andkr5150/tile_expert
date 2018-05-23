<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Url;
use BrianMcdo\ImagePalette\ImagePalette;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Mapping as ORM;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $url = $request->get('parse_html');
        $pic_weight = intval(str_replace(" ", "", $request->get('weight_pic')));
        $pic_height = intval(str_replace(" ", "", $request->get('height_pic')));


        //https://bestcube.space/otkrytki-i-pozdravleniya-s-dnem-rozhdeniya-muzhchine
        //https://bestcube.space/prikolnye-kartinki-i-demotivatory-pro-osen

        try {
            if (!empty($url)) {
                $html = file_get_contents($url);

                $crawler = new Crawler($html);
                $data = $crawler->filter('img');
                $em = $this->getDoctrine()->getManager();


                $arr = array();
                foreach ($data as $tag) {
                    $t_url = $tag->getAttribute('src');
                    if ((strpos($t_url, 'http') === false) && (strpos($t_url, '//') === 0)) {
                        $t_url = 'http:' . $t_url;
                    }
                    if ((!empty($tag)) && (!(strpos($t_url, 'http') === false))) {
                        $str_url = array();

                        $t_url = explode("?", $t_url);
                        $t_url = $t_url[0];
                        $str_url['url'] = $t_url;
//                            var_dump($t_url);

                        if ($this->is_image($t_url)) {
                            $size = getimagesize($str_url['url']);
                            $str_url['size'] = 'Real:' .'[' .$size[0] . ',' . $size[1] . ']';

                            /*
                             * weight = size[0]
                             * height = size[1]
                             */
                            if (($size[0] > $pic_weight) && ($size[1] > $pic_height)) {
                                if ($size[1] >= 200) {
                                    $ss = 200 / $size[1];
                                    $str_url['weight'] = $size[0] * $ss;
                                    $str_url['height'] = 200;

                                    $palette = new ImagePalette($str_url['url']);
                                    $str_url['color'] = array();
                                    foreach ($palette as $color) {
                                        array_push($str_url['color'], $color->rgbaString);
                                    }

                                    $entry = new Url();
                                    $entry->setUrlText($str_url['url']);
                                    $em->persist($entry);

                                    array_push($arr, $str_url);
                                }
                            }
                        }
                    }
                }
                $em->flush();
                $data = $arr;
            } else {
                $emt = array(
                    'url' => 'empty',
                    'size' => '',
                    'weight' => '',
                    'height' => ''
                );
                $data = array($emt);
            }
        } catch (Exception $e) {
            $emt = array(
                'url' => 'Выброшено исключение: ' . $e->getMessage(),
                'size' => '',
                'weight' => '',
                'height' => ''
            );
            $data = array($emt);
        }

        return $this->render('default/index.html.twig', array('datas' => $data));
    }

    /**
     * @Route("/view", name="view")
     */
    public function viewAction(Request $request)
    {
        $entry = $this->getDoctrine()->getRepository(Url::class);
        $entrys = $entry->findAll();

        return $this->render('default/view.html.twig', array('datas' => $entrys));
    }


    public function is_image($filename)
    {
        $is = @getimagesize($filename);
        if (!$is) {
            return false;
        } elseif (!in_array($is[2], array(1,2,3))) {
            return false;
        } else {
            return true;
        }
    }
}
