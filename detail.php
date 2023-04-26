<?php //include_once('connect.php'); ?>
<?php
   if (isset($_COOKIE['product_ids'])) {
  $product_ids_string = $_COOKIE['product_ids'];
  $product_ids = explode(',', $product_ids_string);
} else {
  $product_ids = array();
}

// add the new product ID
$product_ids[] = $_GET['id']; // for example, add product ID 6
$final_products_array = array_unique($product_ids);
$product_ids_string = implode(',', $final_products_array);

setcookie('product_ids', $product_ids_string, time() + 3600, '/');

?>
<?php include_once('topbar.php'); ?>
<?php
    // collect details of the product
    $product_id = $_GET['id'];
    $sql = "SELECT pro.*, cat.name as cat_name from products pro left join categories cat on pro.category_id = cat.id  where pro.id='$product_id' ";
    $details_q = mysqli_query($con, $sql);
    $details = mysqli_fetch_array($details_q);
    
    $cat_id = $details['category_id'];

    //related products
    $related_sql = "SELECT * from products where id != $product_id and category_id='$cat_id' order by rating desc limit 4";
    $related_q = mysqli_query($con, $related_sql);
    // $related = mysqli_fetch_array($related_q);


    //time calculation
    function timeConverter($date){
        // $date = "2023-02-08 03:20:30";
        $today = date('Y-m-d h:i:s');
        $result = 0;
        $differ = abs(strtotime($today) - strtotime($date));
        echo ($differ/1000)."<br>";
        if($differ < 60){ //seconds
            $result = 'few seconds ago';
        }
        else if($differ < 60*60){ //minutes
            $result = intval($differ/60)." mins ago";
        }
        else if($differ < 60*60*24){ //hours
            $result = intval($differ/(60*60))." hours ago";
        }
        else if($differ < 60*60*24*30){ //days
            $result = intval($differ/(60*60*24))." days ago";
        }
        else if($differ < 60*60*24*30*12){ //months
            $result = intval($differ/(60*60*30*24))." months ago";
        }
        return $result;
        
    }
    

        $pro = $_GET['id'];

        $sql = "SELECT * from reviews where product_id='$pro'";
        $comment_q = mysqli_query($con, $sql);

        //probaility error
       
        if(empty(mysqli_fetch_array($comment_q))){
            echo 'no reviews';
        }       
         
    // }
    // echo bayes();die;
   
?>
<!-- Details -->
<section id="prodetails" class="section-p1 my-4">
    
    <div class="pro-image my-5 text-center">
        <img src="<?php echo 'dashboard/uploads/'. $details['image'] ?>" style="height:480px"  class="img-fluid mt-5 px-2" id="" alt="<?php echo $details['tags'] ?>"><br>
        <span><b>Rating:</b> <?php 
        // echo $details['rating']; die;
        if($details['rating'] == 0 ){
            for($i=0;$i<5;$i++):
            ?>
                <i class="fa-regular fa-star text-warning"></i>
            <?php
            endfor;
        }
       if($details['rating']>0){
            for($i=0;$i<intval($details['rating']);$i++):
            ?>
            <i class="fa-solid fa-star text-warning"></i>
                <?php
            endfor;
            for($i=0;$i<(5-intval($details['rating']));$i++):
            ?>
            <i class="fa-regular fa-star text-warning"></i>
            <?php
            endfor;
        }
        ?></span>
    </div>

    <div class="pro-detail position-relative" style="background-color: ;margin-top: 50px;">
        
                <div class="cart-detail col-4 position-absolute" style="right:0" id="cart_box">
                <?php
        // echo $_SESSION['user_role']; die;
            if(!!empty($_SESSION['user_role'])){
                $user = $_SESSION['user_id'];
                $cart_sql = "SELECT pro.*, sum(pro.id) as total_cart_price, count(*) as cart_counter, car.* from carts car left join products pro on pro.id = car.product_id where car.user_id='$user'";
                $cart_q = mysqli_query($con, $cart_sql);
                $cart_item =  mysqli_fetch_array($cart_q);
                ?>
            <span class="w-100 text-center text-light bg-green d-flex px-1">
                Cart Items
            </span>
            <ul class="mx-1 px-1">
                <li class="d-flex justify-content-between bg-light py-2">
                    <span>Items</span>
                    <span><?php echo $cart_item['cart_counter']; ?></span>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span>Total Cost</span>
                    <span><?php echo !!$cart_item['total_cart_price'] ? $cart_item['total_cart_price'] : 0; ?></span>
                </li>
                <li class="d-flex justify-content-between py-2 bg-light">
                    <span>Pay via</span>
                    <span>Khalti</span>
                </li>
                <li class="d-flex justify-content-center py-2">
                    <a href="./dashboard/cart.php" class="bg-green w-50 text-light text-center">View Detail</a>
                </li>
            </ul>
        </div>
        <?php
            }
        ?>
        <h6>Home / <?php echo $details['cat_name'] ?></h6>
        <div id="cart_msg" style="font-weight:bold;color:green"></div>
        <div style="display: flex;justify-content: space-between;width: 100%;background-color: ;align-items: center;">
        
            <span style="display: flex;">
                <h4 class="text-capitalize"><?php echo $details['name'] ?></h4>
            </span>
           
        </div>
        <h2>NRs. <?php echo $details['price'] ?> /-</h2>
        <!-- <select>
            <option>Choose</option>
            <option>Buy</option>
            <option>Rent</option>
        </select> -->
        <?php 
            if(empty($_SESSION['user_id'])){
                ?>
                    <a href="login.php">
                        <button class="button1 btn btn-sm">
                        Buy Now
                        </button>
                    </a>
                    <a href="login.php">
                        <button class="button1 btn btn-sm">
                            Rent Now
                        </button>
                    </a>
                    <a href="login.php">
                        <button class="button1 btn btn-sm" >Add to cart </button>
                    </a>
                <?php
            }
            else{
                ?>
                <a href="order.php?id=<?php echo $details['id'] ?>&type=buy">
                            <button class="button1 btn btn-sm">
                            Buy Now
                            </button>
                        </a>
                        <a href="order.php?id=<?php echo $details['id'] ?>&type=rent">
                            <button class="button1 btn btn-sm">
                                Rent Now
                            </button>
                        </a>
                        <?php if(!empty($_SESSION['user_id'])){
                            ?>
                            <button class="button1 btn btn-sm" role="button" data-product-id="<?php echo $details['id'] ?>" style="padding:4px;" id="add_to_cart" >Add to cart </button>
                            <?php
                        }?>
                <?php
            }
        ?>
        <h4>Product Details</h4>
        <span>
            <?php echo $details['details'] ?>
        </span>
        <br>
        <?php
        //if user is logged in comment on product
        if(empty($_SESSION['user_id'])){

        }
        else{
            ?>
            <form method="post" onsubmit="return(comments())">
            <div id="green" style="color:green;font-weight:bold;">
            </div>
           <div class="form-group d-inline-flex mt-5">
            
                <img src="<?php echo userAvatar($_SESSION['user_id'], $con);  ?>" alt="" class="mx-1" style="height:40px;width:40px;border-radius:50%;border:2px solid white; outline:2px solid silver">
                <input class="form-control col-9" name="user_comment" type="text" placeholder="Comment..." id="user_comment">
                <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <input type="hidden" id="product_id" name="product_id" value="<?php echo $_GET['id']; ?>">
                <button name="comment">Comment</button>
           </div>
        </form><?php
        }
        ?>
        <div  id="comment_list">
            <?php
                // echo "<br> the +ve prob is ".$pos_probability." and -ve prob is ".$neg_probability;
            ?>
        <details class="mt-3" style="max-height:400px;overflow-y:scroll">
            <summary><b>View Comments</b></summary>
            <?php 
                $sql = "SELECT * from reviews where product_id='$product_id' order by id desc limit 12";
                $pro = mysqli_query($con, $sql);

            ?>
            <!-- comments list -->
            <?php if(!empty($pro)){
                foreach($pro as $item):
            ?>
            <div class="form-group mt-2 d-flex justify-content-between">
               <span class="col-9 d-inline-flex " >
                <img src="<?php echo userAvatar($item['user_id'], $con); ?>" alt="" class="mx-1" style="height:40px;width:40px;border-radius:50%;border:2px solid white; outline:2px solid silver">
                    <div class="border-0 d-flex align-items-center px-2" ><?php echo $item['comments']; ?> </div>
               </span>
               <span class="col-3 d-flex align-items-center">
                <i><span class="text-muted"><?php
                echo ($item['date']);
                ?></span></i>
               </span>
           </div>
           <?php endforeach;
           }else{
                    echo "No comments!";
           }
           ?>
        </details>
        </div>
        
    </div>
    
</section>

<!-- Related Products -->
   <section class="mx-3 my-5">
   <h2 class="text-center py-3">Related Products</h2>
    <div class="d-flex justify-content-center">
    <?php
        while($item=mysqli_fetch_array($related_q)):
            ?>
            <div class="col-4 text-center cat_products">
            <a href="detail.php?id=<?php echo $item['id'] ?>">
        <img src="<?php echo './dashboard/uploads/'.$item['image'] ?>" alt="" style="height:120px;width:auto">
        <div class="des">
            <h5><?php  echo $item['name'] ?></h5>
                <?php
                    if($item['rating'] == 0 ){
                    for($i=0;$i<5;$i++):
                    ?>
                        <i class="fa-regular fa-star text-warning"></i>
                    <?php
                    endfor;
                }
            if($item['rating']>0){
                    for($i=0;$i<intval($item['rating']);$i++):
                    ?>
                    <i class="fa-solid fa-star text-warning"></i>
                        <?php
                    endfor;
                    for($i=0;$i<(5-intval($item['rating']));$i++):
                    ?>
                    <i class="fa-regular fa-star text-warning"></i>
                    <?php
                    endfor;
                }
            ?>
            <!-- <a href="#"><i class="fa-light fa-cart-shopping cart"></i></a> -->
        </div>
            </div>
            </a>
        <?php
        endwhile;
    ?>
    </div>
   </section>

   <?php
        function userAvatar($id, $con){
            $user_q = "SELECT * from  users  where id=$id";
            $user = mysqli_query($con, $user_q);

            $avatar = mysqli_fetch_array($user)['avatar'];
            if(!!$avatar){
                return 'dashboard/uploads/'.$avatar;
            }
            else{
                return './images/user.jpg';
            }
            
            
        }
   ?>

  <!-- Footer -->
<?php include_once('foot.php') ?>

    <script>
        var MainImg = document.getElementById("MainImg");
        var smallimg = document.getElementsByClassName("small-img");
        smallimg[0].onclick=function(){
            MainImg.src = smallimg[0].src;
        }
        smallimg[1].onclick=function(){
            MainImg.src = smallimg[1].src;
        }
        smallimg[2].onclick=function(){
            MainImg.src = smallimg[2].src;
        }
        smallimg[2].onclick=function(){
            MainImg.src = smallimg[3].src;
        }
    </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script>
   

//comment via ajax
function comments(){
    var user_comment = document.getElementById('user_comment').value;
    var user_id = document.getElementById('user_id').value;
    var product_id = document.getElementById('product_id').value;
 

    $.ajax({
        url:'comment.php',
        type:'POST',
        data:{user_id, user_comment, product_id},
        success: function(res){
            // alert('test');
            document.getElementById('green').innerHTML=res;
            $("#comment_list").load(" #comment_list" );
            // console.log(JSON.parse(res))
        },
        error: function(err){
            console.log(err)
        }
    })

    return false;
}
  </script>
  <script>
    // cart items
    var add_to_cart = document.getElementById('add_to_cart');
    add_to_cart.addEventListener('click', function(){
        var product_id = add_to_cart.getAttribute('data-product-id');
        // alert(product_id);
        $.ajax({
            url:'cart.php',
            type: 'post',
            data: {product_id},
            success: function(res){
                alert(res);
                $("#cart_counts").load(" #cart_counts" );
                $("#cart_box").load(" #cart_box" );

            },
            error: function(){

            }
        })
    })
  </script>
  </body>
</html>