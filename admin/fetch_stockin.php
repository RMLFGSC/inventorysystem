<?php
include("../conn.php");

if (isset($_POST['stockin_id'])) {
  $stockin_id = $_POST['stockin_id'];
  $query = "SELECT * FROM stock_in WHERE stockin_id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $stockin_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  ?>
  <div class="form-row">
    <div class="form-group col-md-4 col-12">
      <label>Item</label>
      <input type="text" name="item" class="form-control" value="<?php echo $row['item']; ?>" required>
    </div>
    <div class="form-group col-md-4 col-12">
      <label>Quantity</label>
      <input type="number" name="qty" class="form-control" value="<?php echo $row['qty']; ?>" required>
    </div>
    <div class="form-group col-md-4 col-12">
      <label>Serial Number</label>
      <input type="text" name="serialNO" class="form-control" value="<?php echo $row['serialNO']; ?>" required>
    </div>
    <div class="form-group col-md-12">
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="warranty" name="warranty" value="1" <?php echo ($row['warranty'] == 1) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="warranty">With Warranty?</label>
      </div>
    </div>
  </div>
  <?php
}
?>
