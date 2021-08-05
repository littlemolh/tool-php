<?php
// +----------------------------------------------------------------------
// | Little Mo - Tool [ WE CAN DO IT JUST TIDY UP IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://ggui.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: littlemo <25362583@qq.com>
// +----------------------------------------------------------------------

namespace littlemo\tool;

class WordToHtml
{

    /**
     * docx文件转html内容
     *
     * @description
     * @example
     * @author LittleMo 25362583@qq.com
     * @since 2021-08-05
     * @version 2021-08-05
     * @param string $file 文件路径，仅支持docx类型
     * @return array
     */
    public function get($file = '')
    {
        $result = [
            'name' => basename($file)
        ];
        try {
            //code...

            $ext_name = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext_name != 'docx') {
                throw new \Exception('文件格式不支持，请上传docx格式word文件', 401);
            }
            $filesize = filesize($file);
            $unit = 'B';
            if ($filesize > 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "K";
            }
            if ($filesize > 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "M";
            }
            if ($filesize > 1024) {
                $filesize = bcdiv($filesize, 1024, 2);
                $unit = "G";
            }
            $result['size'] = $filesize;
            $result['unit'] = $unit;
            $html = $this->wordParsing($file);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            //throw $th;
        }
        $result['html'] = $html ?? '';
        return $result;
    }

    //解析word内容并返回html
    public function wordParsing($source)
    {
        //加载word文件 并 通过getSections获取word文档的全部元素
        $sections = \PhpOffice\PhpWord\IOFactory::load($source)->getSections();

        //定义html变量用于存储word文本内容
        $html = '';

        //循环所有元素
        foreach ($sections as $section) {

            //获取当前元素的所有子元素
            $elements = $section->getElements();

            //循环当前子元素
            foreach ($elements as $eky => $evl) {
                $html .= '<p>';
                if ($evl instanceof \PhpOffice\PhpWord\Element\TextRun) { //判断是否普通文本

                    $content_elements = $evl->getElements();
                    foreach ($content_elements as $eky2 => $evl2) {
                        $html .= $this->elementHandler($evl2, $evl);
                    }
                } elseif ($evl instanceof \PhpOffice\PhpWord\Element\PreserveText) { //判断是否保留元素(如自动生成链接的网址元素)
                    $data = $evl->getText();
                    $find = array('{', 'HYPERLINK', '}', ' ', '"', 'f', 'g');
                    $replace = '';
                    $resText = str_replace($find, $replace, $data);
                    if (isset($resText)) {
                        $html .= $resText[0];
                    }
                } elseif ($evl instanceof \PhpOffice\PhpWord\Element\Table) {
                    $all_table_elements = $evl->getRows();
                    $html .= '<table style="margin:0;padding:0;border-collapse:collapse;border-spacing:0;" >';
                    foreach ($all_table_elements as $tky => $tvl) {
                        $html .= '<tr style="padding:0">';
                        $all_table_cells = $tvl->getCells();
                        foreach ($all_table_cells as $cky => $cvl) {
                            $cell_elements = $cvl->getElements();

                            //获取表格宽度(返回单位为：缇)
                            $td_width = $cvl->getWidth();
                            $td_width_px = round($cvl->getWidth() / 15, 0);

                            $html .= '<td style="border: 1px solid #777777;padding:2px 5px;width:' . $td_width_px . '">';
                            foreach ($cell_elements as $cl) {

                                //判断当存在elements属性时执行
                                if (property_exists($cl, 'elements')) {
                                    $content_elements = $cl->getElements();
                                    foreach ($content_elements as $eky2 => $evl2) {
                                        $html .= $this->elementHandler($evl2, $cl);
                                    }
                                }
                            }
                            $html .= '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                }
                $html .= '</p>';
            }
            return $html;
        }
    }

    //元素内容数据处理，$end_element最末级元素，是$parent_element的子元素；$parent_element为当前元素
    public function elementHandler($end_element, $parent_element)
    {
        $html = '';
        if ($end_element instanceof \PhpOffice\PhpWord\Element\Text) { //判断是否普通文本

            $style = $end_element->getFontStyle();
            //$fontFamily = mb_convert_encoding($style->getName(), 'GBK', 'UTF-8');
            $fontFamily = $style->getName();
            $fontSize = $style->getSize() ? ($style->getSize() / 72) * 96 : '';
            $isBold = $style->isBold();
            $fontcolor = $style->getColor();
            $styleString = '';
            $fontFamily && $styleString .= "font-family:{$fontFamily};";
            $fontSize && $styleString .= "font-size:{$fontSize}px;";
            $isBold && $styleString .= "font-weight:bold;";
            $fontcolor && $styleString .= "color:{$fontcolor};";
            $html .= sprintf(
                '<span style="%s">%s</span>',
                $styleString,
                $end_element->getText()
                //mb_convert_encoding($evl2->getText(), 'GBK', 'UTF-8')
            ); //dump($end_element->getText());

        } elseif ($end_element instanceof \PhpOffice\PhpWord\Element\Link) {  //判断是否链接

            $style = $end_element->getFontStyle();
            //$fontFamily = mb_convert_encoding($style->getName(), 'GBK', 'UTF-8');
            $fontFamily = $style->getName();
            $fontSize = $style->getSize() ? ($style->getSize() / 72) * 96 : '';
            $isBold = $style->isBold();
            $fontcolor = $style->getColor();
            $styleString = '';
            $fontFamily && $styleString .= "font-family:{$fontFamily};";
            $fontSize && $styleString .= "font-size:{$fontSize}px;";
            $isBold && $styleString .= "font-weight:bold;";
            $fontcolor && $styleString .= "color:{$fontcolor};";
            $html .= sprintf(
                '<a href="%s" style="%s">%s</a>',
                $end_element->getSource(),
                $styleString,
                $end_element->getText()
                //mb_convert_encoding($evl2->getText(), 'GBK', 'UTF-8')
            );
        } elseif ($end_element instanceof \PhpOffice\PhpWord\Element\Image) { //判断是否图片
            //可以在这里执行自定义方法将图片上传到OSS或者图片服务器哈
            $imageDataTmp = $end_element->getImageStringData(true);
            $imageType = $end_element->getImageType() ? $end_element->getImageType() : 'image/jpg';
            $imageData = 'data:' . $imageType . ';base64,' . str_replace(array("\r\n", "\r", "\n"), "", $imageDataTmp);

            //保存文件
            //$imageSrc = './uploads/' . md5($end_element->getSource()) . '.' . $end_element->getImageExtension();
            //file_put_contents($imageSrc,base64_decode(explode(',',$imageData)[1]));

            $html .= '<img src="' . $imageData . '" style="width:100%;height:auto">';
        }
        return $html;
    }
}
