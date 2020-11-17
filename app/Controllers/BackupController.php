<?php
namespace App\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BackupController
{
    public $connection;
    public function __construct(){
      $this->connection = mysqli_connect('localhost','root','','nenpos');
    }
    public function connect($args){
      if($args['host'] && $args['user'] && $args['table']){
        $this->connection = mysqli_connect($args['host'],$args['user'] || '',$args['pass'],$args['table']);
      }
    }
    public function backup(Request $request, Response $response){
      connect($request->getParsedBody());
      $tables = array();
      $result = mysqli_query($this->connection,"SHOW TABLES");
      while($row = mysqli_fetch_row($result)){
        if($row[0]!="product_history" && $row[0]!="product_cost_history" && $row[0]!="product_price_history")
          $tables[] = $row[0];
      }
      $return = '';
      foreach($tables as $table){
        $result = mysqli_query($this->connection,"SELECT * FROM ".$table);
        $num_fields = mysqli_num_fields($result);
        
        $return .= 'DROP TABLE '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($this->connection,"SHOW CREATE TABLE ".$table));
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
      connect($request->getParsedBody());
      ini_set('memory_limit', '-1');
      $filename = 'backup.sql';
      $handle = fopen($filename,"r+");
      $contents = fread($handle,filesize($filename));
      $sql = explode(';',$contents);
      foreach($sql as $query){
        $result = mysqli_query($this->connection,$query);
      }
      fclose($handle);
      return $response->withJson([
        "success"=>true
      ]);
    }
    // public function erase(){
    //   $tables = array();
    //   $result = mysqli_query($this->connection,"SHOW TABLES");
    //   while($row = mysqli_fetch_row($result)){
    //     if($row[0]!="users")
    //       $tables[] = $row[0];
    //   }
    //   $return = '';
    //   foreach($tables as $table){
    //     $query = "TRUNCATE ".$table;
    //     mysqli_query($this->connection,$query);
    //   }
    // }
}