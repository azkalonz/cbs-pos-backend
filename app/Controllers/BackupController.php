<?php
namespace App\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BackupController
{
    public function get_connection($args){
      if(isset($args['host']) && isset($args['user']) && isset($args['table'])){
        return mysqli_connect($args['host'],$args['user'],isset($args['pass'])?$args['pass']:"",$args['table']);
      } else {
        return mysqli_connect('localhost','root','','nenpos');
      }
    }
    public function backup(Request $request, Response $response){
      $tables = array();
      $result = mysqli_query($this->get_connection($request->getParsedBody()),"SHOW TABLES");
      while($row = mysqli_fetch_row($result)){
        if($row[0]!="product_history" && $row[0]!="product_cost_history" && $row[0]!="product_price_history")
          $tables[] = $row[0];
      }
      $return = '';
      foreach($tables as $table){
        $result = mysqli_query($this->get_connection($request->getParsedBody()),"SELECT * FROM ".$table);
        $num_fields = mysqli_num_fields($result);
        
        $return .= 'DROP TABLE '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($this->get_connection($request->getParsedBody()),"SHOW CREATE TABLE ".$table));
        $return .= "\n\n".$row2[1].";\n\n";
        
        for($i=0;$i<$num_fields;$i++){
          while($row = mysqli_fetch_row($result)){
            $return .= "INSERT INTO ".$table." VALUES(";
            for($j=0;$j<$num_fields;$j++){
              $row[$j] = addslashes($row[$j]);
              if(isset($row[$j])){ $return .= '"'.$row[$j].'"';}
              else{ $return .= '""';}
              if($j<$num_fields-1){ $return .= ',';}
            }
            $return .= ");\n";
          }
        }
          $return .= "\n\n\n";
        }
        //save file
        $handle = fopen("backup.sql","w+");
        fwrite($handle,$return);
        fclose($handle);
        return $response->withJson([
          "success"=>true
        ]);
    }
    public function restore(Request $request, Response $response){
      ini_set('memory_limit', '-1');
      $filename = 'backup.sql';
      $handle = fopen($filename,"r+");
      $contents = fread($handle,filesize($filename));
      $sql = explode(';',$contents);
      foreach($sql as $query){
        $result = mysqli_query($this->get_connection($request->getParsedBody()),$query);
        if(!$result){
          return $response->withJson([
            "success"=>false,
            "error"=>"Something went wrong"
          ]);
        }
      }
      fclose($handle);
      return $response->withJson([
        "success"=>true
      ]);
    }
    // public function erase(){
    //   $tables = array();
    //   $result = mysqli_query($this->get_connection($request->getParsedBody()),"SHOW TABLES");
    //   while($row = mysqli_fetch_row($result)){
    //     if($row[0]!="users")
    //       $tables[] = $row[0];
    //   }
    //   $return = '';
    //   foreach($tables as $table){
    //     $query = "TRUNCATE ".$table;
    //     mysqli_query($this->get_connection($request->getParsedBody()),$query);
    //   }
    // }
}