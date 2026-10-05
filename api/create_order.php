<?php
require __DIR__.'/_common.php';
try {
  $in=body_json(); $name=trim($in['customerName']??''); $mobile=trim($in['customerMobile']??''); $items=$in['items']??[];
  if($name==='' || !is_array($items) || count($items)<1) respond(['error'=>'Customer name and at least one item are required.'],400);
  $clean=[]; $total=0.0;
  foreach($items as $i){$n=trim((string)($i['name']??''));$q=(float)($i['qty']??0);$r=(float)($i['rate']??-1);if($n===''||$q<=0||$r<0)respond(['error'=>'Invalid item data.'],400);$a=round($q*$r,2);$total+=$a;$clean[]=[$n,$q,$r,$a];}
  $orderNo='ORD-'.gmdate('Ymd').'-'.strtoupper(bin2hex(random_bytes(3))); $token=bin2hex(random_bytes(32));
  $pdo=db();$pdo->beginTransaction();
  $s=$pdo->prepare("INSERT INTO orders(order_no,qr_token,customer_name,customer_mobile,total_amount,status) VALUES(?,?,?,?,?,'ACTIVE') RETURNING id,created_at");
  $s->execute([$orderNo,$token,$name,$mobile,round($total,2)]);$o=$s->fetch();
  $si=$pdo->prepare('INSERT INTO order_items(order_id,item_name,quantity,rate,amount) VALUES(?,?,?,?,?)');
  foreach($clean as $i)$si->execute([$o['id'],$i[0],$i[1],$i[2],$i[3]]);
  $pdo->commit(); respond(['orderNo'=>$orderNo,'token'=>$token,'total'=>round($total,2),'status'=>'ACTIVE','createdAt'=>$o['created_at']]);
} catch(Throwable $e){if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();error_log($e);respond(['error'=>'Unable to create order.'],500);}
