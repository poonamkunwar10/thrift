<?php
include_once('../connect.php');
include('top.php');
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

//for all categories
switch($user_role){
    case 'admin':
        $sql = "SELECT pro.*, ord.*, user.name as buyer, ship.address as shipping
        from orders ord 
        left join products pro on ord.product_id=pro.id
        left join users user on ord.user_id=user.id
        left join shippings ship on ord.shipping_id=ship.id
        where ord.type='rent' order by ord.date desc";
        break;
    case 'seller':
        $sql = "SELECT pro.*, ord.*, user.name as buyer, ship.address as shipping
        from orders ord 
        left join products pro on ord.product_id=pro.id
        left join users user on ord.user_id=user.id
        left join shippings ship on ord.shipping_id=ship.id
        where ord.type='rent' and pro.user_id='$user_id' order by ord.date desc";
        break;
    default: 
        $sql = "SELECT pro.*, ord.*, user.name as seller, ship.address as shipping
        from orders ord 
        left join products pro on ord.product_id=pro.id
        left join users user on ord.user_id=user.id
        left join shippings ship on ord.shipping_id=ship.id
        where ord.type='rent' and ord.user_id='$user_id' order by ord.date desc";
}

$rents = mysqli_query($con, $sql);

 ?>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <h3 class="bg-success text-light w-100 p-2">Rents</h3>
                </div>
                <table class="table table-striped w-100 ">
                        <thead>
                            <tr class="text-capitalize">
                                <th>date</th>
                                <th>user</th>
                                <th>product</th>
                                <th>seller</th>
                                <th>address</th>
                                <th>total</th>
                                <?php if($user_role == 'admin'){
                                    ?>
                                    <th>status</th>
                                    <?php
                                } ?>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while($items=mysqli_fetch_array($rents)){
                               
                                ?>
                                    <tr class="">
                                        <td><?php echo $items["date"]; ?></td>
                                        <td><?php echo (isset($items['buyer'])) ? $items['buyer'] : 'self' ?></td>
                                        <td><?php echo $items['name']; ?></td>
                                        <td><?php echo (isset($items['seller'])) ? $items['seller'] : 'self' ?></td>
                                        <td><?php echo $items['shipping']; ?></td>
                                        <td><?php echo $items['price']; ?></td>
                                        <?php if($user_role == 'admin'){
                                    ?>
                                    <td>
                                            <?php 
                                                $pro_status = checkStatus($items['product_id'], $con);
                                                if($pro_status == 0){
                                                    ?>
                                                    <a href="resale.php?id=<?php echo $items['product_id'] ?>&ord_id=<?php echo $items['id'] ?>">
                                                        <button class="btn btn-xs btn-primary">Resale</button>
                                                    </a>
                                                    <?php
                                                }
                                            ?>
                                        </td>
                                    <?php
                                }
                                ?>
                                        
                                    </tr>
                                <?php
                            }
                            ?>
                            <tr class="text-right"><td colspan="5"> <a href="">View All</a></td></tr>
                        </tbody>
                    </table>
            </div>
            <!-- /.card -->
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  
  <?php
   function checkStatus($pro_id, $con){
        $pro_q = "SELECT status from products where id='$pro_id'";
        $pro = mysqli_query($con, $pro_q);
        $res =  mysqli_fetch_row($pro)[0];
        return $res;

   }
  ?>

  <?php include('foot.php'); ?>