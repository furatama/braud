<?php

namespace App\BDSM;

use Maatwebsite\Excel\Facades\Excel;

class ExcelHelper extends Excel  {

    private $header = [];
    private $data = [];
    private $dataProps = [];
    private $footer = [];
    private $sides;
    private $sheetName;

    private static $xls = [];

    public function __construct($name = "Sheet") {
      $this->sheetName = $name;
      array_push(self::$xls,$this);
    }

    private static function c($x,$y) {
      return chr($x + 64) . $y;
    }

    private static function cc($x1,$y1,$x2,$y2) {
      return self::c($x1,$y1) . ':' . self::c($x2,$y2);
    }

    public function setHeaderMeta($meta,$string,$color="#000000",$size=12,$align="center",$weight="normal") {
      $this->header[$meta] = [
        "string" => $string,
        "color" => $color,
        "align" => $align,
        "size" => $size,
        "weight" => $weight
      ];
    }

    public function setTitle($string,$color="#000000",$size=20,$align="center",$weight="bold") {
      $this->setHeaderMeta('title',$string,$color,$size,$align,$weight);
    }

    public function setSubTitle($string,$color="#000000",$size=16,$align="center",$weight="bold") {
      $this->setHeaderMeta('subtitle',$string,$color,$size,$align,$weight);
    }

    public function setData($data,$headBG="#ffffff",$headText="#000000",$bodyBG="#ffffff",$bodyText="#000000") {
      $this->data = $data;
      $this->dataProps = [
        "headBG" => $headBG,
        "headText" => $headText,
        "bodyBG" => $bodyBG,
        "bodyText" => $bodyText,
      ];
    }

    public static function render($filename = 'excel', $title = "title", $desc = 'Laporan Excel') {

      // dd(self::$xls);

      Excel::create($filename, function($excel) use($title,$desc) {

        $excel->setTitle($title);
        $excel->setCreator('Byte Dream S&M')->setCompany('Byte Dream S&M');
        $excel->setDescription($desc);

        foreach (self::$xls as $xlsheet) {

          $excel->sheet($xlsheet->sheetName, function($sheet) use($xlsheet) {

            $sheet->fromArray($xlsheet->data);
            $sheet->prependRow();

            $nHeader = 1;
            $header = [];
            foreach ($xlsheet->header as $key => $value) {
              if ($key != 'subtitle' && $key != 'title') {
                $sheet->prependRow([$xlsheet->header[$key]['string']]);
                $header[$nHeader] = $xlsheet->header[$key];
                $nHeader++;
              }
            }

            if (isset($xlsheet->header['subtitle'])) {
              $sheet->prependRow([$xlsheet->header['subtitle']['string']]);
              $header[$nHeader] = $xlsheet->header['subtitle'];
              $nHeader++;
            }
            if (isset($xlsheet->header['title'])) {
              $sheet->prependRow([$xlsheet->header['title']['string']]);
              $header[$nHeader] = $xlsheet->header['title'];
              $nHeader++;
            }
            
            $lastX = 0;
            $lastY = 0;
            $lastYTable = 0;
            if (count($xlsheet->data) > 0) {
              $lastX = count($xlsheet->data[0]);
              $lastYTable = $nHeader + count($xlsheet->data) + 1;
            }

            for ($i=1; $i < $nHeader; $i++) { 
              $sheet->mergeCells(self::cc(1,$i,$lastX,$i));

              $props = $header[$nHeader - $i];

              $sheet->cell(self::c(1,$i), function($cell) use($props) {
                  $cell->setAlignment($props['align']); 
                  $cell->setFontSize($props['size']);
                  $cell->setFontWeight($props['weight']);
                  $cell->setFontColor($props['color']);
              });
            }

            if ($lastX > 0) {
              $nHeader++;

              $props = $xlsheet->dataProps;
              $sheet->cell(self::cc(1,$nHeader,$lastX,$nHeader), function($cell) use($props) {
                  $cell->setFontColor($props['headText']);
                  $cell->setBackground($props['headBG']);
                  $cell->setFontWeight('bold');
                  $cell->setBorder('thin', 'thin', 'thin', 'thin');
              });
              for($i = 1; $i <= $lastX; $i++) {
                $sheet->cell(self::cc($i,$nHeader,$i,$lastYTable), function($cell) use($props) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell(self::cc($i,$nHeader + 1,$i,$lastYTable), function($cell) use($props) {
                  $cell->setFontColor($props['bodyText']);
                  $cell->setBackground($props['bodyBG']);
                });
              }
            }

            // dump($xlsheet->data);

          });
        }

        // dd();

      })->download('xlsx');

      // Excel::create('excel', function($excel) {

      //   $excel->setTitle('title');
        
      //   $excel
      //   ->setCreator('Byte Dream SM')
      //   ->setCompany('Byte Dream SM');

      //   $excel->setDescription('desc');

      //   $excel->sheet('Sheet 1', function($sheet) use($caption,$data) {

      //     $sheet->fromArray($data);
  
      //     $sheet->prependRow();
          
      //     if (isset($caption['date']))
      //         $sheet->prependRow([$caption['date']]);
      //     else
      //         $sheet->prependRow(['Per ' . date('d/m/Y')]);
      //     $sheet->prependRow([$caption['header']]);

      //     $hasData ? chr(count($data[0]) + 64) : 'A';
  
      //     $hasData = count($data[0]) > 0;
      //     $lastCol = $hasData ? chr(count($data[0]) + 64) : 'A';
  
      //     $sheet->mergeCells('A1:'.$lastCol.'1');
      //     $sheet->mergeCells('A2:'.$lastCol.'2');
  
      //     $sheet->cell('A1', function($cell) {
      //         $cell->setAlignment('center'); 
      //         $cell->setFontSize(20);
      //         $cell->setFontWeight('bold');
      //         $cell->setFontColor('#ff0000');
      //     });
  
      //     $sheet->cell('A2', function($cell) {
      //         $cell->setAlignment('right'); 
      //         $cell->setFontWeight('italic');
      //     });
  
      //     $sheet->cell('A4:'.$lastCol.'4', function($cell) {
      //         $cell->setFontWeight('bold');
      //         $cell->setBackground('#ffff00');
      //         $cell->setAlignment('center');
      //     });
  
      //     if ($hasData) {
      //         for($i = 65; $i < count($data[0]) + 65; $i++) {
      //             $sheet->cell(chr($i).'4:'.chr($i).(count($data)+4), function($cell) {
      //                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
      //             });
      //         }
      //     }
      //   })


      // })->download('xlsx');

    }


}

// private function createFile($caption,$data) {

// Excel::create($caption['filename'], function($excel) use($caption,$data) {

//     // Set the title
//     $excel->setTitle($caption['title']);

//     // Chain the setters
//     $excel->setCreator('Byte Dream SM')
//             ->setCompany('Byte Dream SM');

//     // Call them separately
//     $excel->setDescription($caption['desc']);

//     $excel->sheet('Sheet 1', function($sheet) use($caption,$data) {

//         $sheet->fromArray($data);

//         $sheet->prependRow();

//         if (isset($caption['date']))
//             $sheet->prependRow([$caption['date']]);
//         else
//             $sheet->prependRow(['Per ' . date('d/m/Y')]);
//         $sheet->prependRow([$caption['header']]);

//         $hasData = count($data[0]) > 0;
//         $lastCol = $hasData ? chr(count($data[0]) + 64) : 'A';

//         $sheet->mergeCells('A1:'.$lastCol.'1');
//         $sheet->mergeCells('A2:'.$lastCol.'2');

//         $sheet->cell('A1', function($cell) {
//             $cell->setAlignment('center'); 
//             $cell->setFontSize(20);
//             $cell->setFontWeight('bold');
//             $cell->setFontColor('#ff0000');
//         });

//         $sheet->cell('A2', function($cell) {
//             $cell->setAlignment('right'); 
//             $cell->setFontWeight('italic');
//         });

//         $sheet->cell('A4:'.$lastCol.'4', function($cell) {
//             $cell->setFontWeight('bold');
//             $cell->setBackground('#ffff00');
//             $cell->setAlignment('center');
//         });

//         if ($hasData) {
//             for($i = 65; $i < count($data[0]) + 65; $i++) {
//                 $sheet->cell(chr($i).'4:'.chr($i).(count($data)+4), function($cell) {
//                     $cell->setBorder('thin', 'thin', 'thin', 'thin');
//                 });
//             }
//         }

//     });

// })->download('xlsx');