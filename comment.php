
<?php include_once('connect.php'); ?>
<?php
    // comments
        $comments = $_POST['user_comment'];
        $product_id = $_POST['product_id'];
        $user_id = $_POST['user_id'];
        $sql="INSERT into reviews(id, comments, user_id, product_id) values('', '$comments', $user_id, $product_id)";
        $ins = mysqli_query($con, $sql);
       
        $sql = "SELECT * from reviews where product_id='$product_id'";
        $comment_q = mysqli_query($con, $sql);

        //probaility error
        $neg_probability = $pos_probability = 0;
        // $comments = [array('this','not good','product'), array('its','silly'), array('superv','products', 'sold','ever'), array('not good','contents')];

        // naive bayes for sentimental analysis of the comment
        $comments = [];
        $total_words = 0;
        while($comments_list=mysqli_fetch_array($comment_q)){
            $explode_comments = explode(' ', $comments_list['comments']);
            $total_words+=sizeof($explode_comments);
            array_push($comments, $explode_comments);
        }

        $negative = ['not', 'bad', 'poor', 'silly', 'worst'];
        $positive = ['wow', 'nice','excellent', 'superv', 'beautiful'];

        $neg_count=$pos_count=0;

        for($i=0; $i< sizeof($negative); $i++){

            // each comments
            for($j=0;$j<sizeof($comments);$j++){
                if(in_array($negative[$i], $comments[$j])){
                    $neg_count++;
                }
            }
        }

        for($i=0; $i< sizeof($positive); $i++){
            for($j=0;$j<sizeof($comments);$j++){
                if(in_array($positive[$i], $comments[$j])){
                    $pos_count++;
                }
            }
        }
        $neg_probability = $neg_count/$total_words;
        $pos_probability = $pos_count/ ($neg_count + $pos_count);
        $rating_value = $pos_probability*5;

        if($ins){
            $rate_q = "UPDATE products set rating='$rating_value'  where id='$product_id'";
            $rating = mysqli_query($con, $rate_q);
            if($rating){
                echo json_encode('You commented the product!');
            }
        }
        else{
            return false;
            
        }
        
        
    
        ?>