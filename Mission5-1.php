<?php
    //DB接続設定
    $dsn = '*****';
    $user = '*****';
    $password = '*****';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $name = $_POST["name"];//名前受け取り
    $comment = $_POST["comment"];//コメント受け取り
    $seclet = $_POST["seclet"];//新規or編集見分けコード
    $date =date("Y/m/d H:i");//日付受け取り
    $pass = $_POST["pass"];//パスワード受け取り
    $del = $_POST["delnum"];//削除指定番号受け取り
    $edit = $_POST["editnum"];//編集指定番号受け取り
    $dkey = $_POST["dkey"];//削除パスワード確認受け取り
    $ekey = $_POST["ekey"];//編集パスワード確認受け取り

    if(isset($name)){//入力フォーム
        if($seclet == null){//新規投稿
            if($name != null && $comment != null && $pass != null){
                $sql = $pdo -> prepare("INSERT INTO board (name, comment, date, passward)VALUES (:name, :comment, :date, :passward)");
                $sql -> bindParam(':name', $dbname, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $dbcomment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $dbdate, PDO::PARAM_STR);
                $sql -> bindParam(':passward', $dbpass, PDO::PARAM_STR);
                $dbname = $name;
                $dbcomment = $comment;
                $dbdate = $date;
                $dbpass = $pass;
                $sql -> execute();
                $post_cpl = "投稿を受け付けました。";
            }else{
                $post_not = "入力されていない項目があります";
            }
        }else{
            $id = $seclet; //変更する投稿番号
            $dbname = $name;
            $dbcomment = $comment;
            $dbdate = $date;
            $dbpass = $pass;
            $sql = 'UPDATE board SET name=:name,comment=:comment,date=:date,passward=:passward WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $dbname, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $dbcomment, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $dbdate, PDO::PARAM_STR);
            $stmt -> bindParam(':passward', $dbpass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $edit_cpl = "編集しました。";
        }
    }elseif(isset($del)){//削除機能
        $id = $del;
        $sql = 'SELECT * FROM board WHERE id=:id ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(); 
	    foreach ($results as $row){
            if($dkey == $row['passward']){
                $sql = 'delete from board where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $del_cpl = "削除しました。";
            }else{
                $del_not = "パスワードが違います。";
            }
        } 
    }elseif(isset($edit)){//編集受け付け
        $id = $edit;
        $sql = 'SELECT * FROM board WHERE id=:id ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(); 
	    foreach ($results as $row){
            if($ekey == $row['passward']){
                $edit_number = $row['id'];
                $editname = $row['name'];
                $editcomment = $row['comment'];
                $editpass = $row['passward'];
                $a = "編集中";
            }else{
                $edit_not = "パスワードが違います。";
            }
        } 
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板 Ver.MySQL</title>
</head>
    <body>
        <h1>掲示板 Ver.MySQL</h1>
        <h2>投稿</h2>
        <form action="" method="POST">
            <p><span style="color: #0000ff"><?php echo $a; ?></span></p>
            <p> 名 前 </p>
            <input type="text" name="name" placeholder = "名前" value = <?php echo $editname; ?>>
            <p> コメント </p>
            <input type="text" name="comment" placeholder = "コメント" value = <?php echo $editcomment; ?>>
            <p>パスワード </p>
            <input type="text" name="pass" placeholder = "パスワード" value = <?php echo $editpass; ?>>
            <button type="submit" name="post">投稿</button><br>
            <input type="hidden" name = "seclet" value = <?php echo $edit_number; ?>>
            <span style="color: #0000ff"><?php echo $post_cpl; echo $edit_cpl; ?></span>
            <span style="color: #ff0000"><?php echo $post_not; ?></span>
        </form>    

        <h2>削除</h2>
        <form action="" method="POST">
            <input type="number" name="delnum" placeholder = "削除指定番号">
            <input type="text" name="dkey" placeholder = "パスワード">
            <button type="submit" name="delete">削除</button><br>
            <span style="color: #0000ff"><?php echo $del_cpl; ?></span>
            <span style="color: #ff0000"><?php echo $del_not; ?></span>
        </form>

        <h2>編集</h2>
        <form action="" method="POST">
            <input type="number" name="editnum" placeholder = "編集指定番号">
            <input type="text" name="ekey" placeholder = "パスワード">
            <button type="submit" name="edit">編集</button><br>
            <span style="color: #ff0000"><?php echo $edit_not; ?></span>
        </form>

        <h2>投稿一覧</h2>
            <?php
                $sql = 'SELECT * FROM board';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    echo $row['id'].', ';
                    echo ' 名前 : '.$row['name'];
                    echo ' コメント : '.$row['comment'];
                    echo '  '.$row['date'].'<br>';
                }
            ?>
    </body>
</html>