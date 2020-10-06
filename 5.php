<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset = "utf-8">
<title>mission5</title>
</head>
<body>
    
<?php
    $name=$_POST["name"];               //名前 
    $delete=$_POST["delete"];           //削除番号
    if(isset($_POST["edit"]) && $_POST["submit"]=="編集"){
        $editnum=$_POST["edit"];        //編集要求番号
    } else {
        $editnum="";                  //編集してないときは空白に
    }
    $editnum_run=$_POST["edit_run"];    //編集実行番号
    $comment=$_POST["comment"];         //コメント
    $date=date("Y/m/d H:i:s");         //日時
    $pass_send=$_POST["pass_send"];     //送信時パスワード
    $pass_delete=$_POST["pass_delete"]; //削除時パスワード
    
    // DB接続設定
	$dsn = 'mysql:dbname=***;host=localhost';
	$user = '***';
	$password = '***';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date char(32),"
	. "pass char(32)"
	." );";
	$stmt = $pdo->query($sql);

    // 送信
    if(isset($_POST["name"]) && isset($_POST["comment"]) && $_POST["submit"]=="送信"){
        // 新規入力
        if(empty($_POST["edit_run"])){
            if($pass_send=="2020"){     //パスワードの確認
                $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, pass)
                VALUES (:name, :comment, :date, :pass)");
                // 	bindParam ($パラメータID, 
                // $バインドする変数 [, $PDOデータ型定数[, $PDOデータ型の長さ[, $ドライバーオプション]]] )
	            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	            $sql -> bindParam(':pass', $pass_send, PDO::PARAM_STR);
            	//executeでクエリを実行
	            $sql -> execute();
            } else {
                echo "パスワードが間違っています<br>";
            }
        } else {
        //編集実行
            if($pass_send=="2020"){
                $id_run = $editnum_run;
                $sql = 'UPDATE tbtest SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt -> bindParam(':id', $id_run, PDO::PARAM_INT);
                $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
	            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
	            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
	            $stmt -> bindParam(':pass', $pass_send, PDO::PARAM_STR);
	            $stmt -> execute();
            } else {
                echo "パスワードが間違っています<br>";
            }
        } 
    } 
    //編集
    else if(isset($_POST["edit"]) && $_POST["submit"]=="編集"){
        $id=$editnum;

        $sql = 'SELECT * FROM tbtest WHERE id='.$id;    
        $stmt = $pdo->prepare($sql); 
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();
        $results = $stmt -> fetchAll();
        
        foreach($results as $result){
            $editnum=$id;
            $editname=$result['name'];
            $editcom=$result['comment'];
        }    
    }
    // 削除
    else if(isset($_POST["delete"]) && $_POST["submit"]=="実行"){
        $id = $delete;
        //データを取り出してから削除実行
        $sql = 'SELECT * FROM tbtest WHERE id='.$id;
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();
        if($pass_delete=="2020"){
            $sql = 'delete from tbtest where id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
	        $stmt -> execute();
        } else {
            echo "パスワードが間違っています<br>"; 
        }         
    }

?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <strong>この掲示板のテーマ</strong><br>
    _________________________________<br><br>
    送信フォーム<br>
    <form method="post" action"">
        <input type="text" name="name" placeholder="名前" 
        value="<?php if(isset($editname)) {echo $editname;} ?>">    <!--編集時に表示-->
        <input type="text" name="comment" placeholder="コメント"
        value="<?php if(isset($editcom)) {echo $editcom;} ?>">  <!--編集時に表示-->
        <input type="submit" name="submit" value="送信"><br>
        <!--パスワード-->
        <input type="text" style="ime-mode:disabled;"
                name="pass_send" placeholder="パスワード"><br>   <!--me-mode:disabled：英数字のみ-->

    削除フォーム<br>
       
        <input type="number" name="delete" placeholder="削除番号">   
        <input type="submit" name="submit" value="実行"><br>
                <!--パスワード-->
        <input type="text" style="ime-mode:disabled;"
                name="pass_delete" placeholder="パスワード"><br>   <!--me-mode:disabled：英数字のみ-->

    編集フォーム<br>
      
        <input type="number" name="edit" placeholder="編集対象番号">
        <input type="submit" name="submit" value="編集"><br>
        
    <!--編集時に利用-->

        <!--hidden:非表示-->
        <input type="hidden" name="edit_run"    
        value="<?php if(isset($editnum)) {echo $editnum;} ?>"> 
    </form>

</body>
</html>

<?php 
    $sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	//fetchall:すべての結果業を含む配列を返す
	$results = $stmt->fetchAll();
    foreach($results as $result){
        echo    $result['id']." ";
        echo    $result['name']." ";
        echo    $result['comment']." ";
        echo    $result['date']." ";
        echo    "<br>";
        echo    "<hr>";
    }

?>