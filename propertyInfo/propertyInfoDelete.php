<?php include('propertyInfoTopHalf.php'); ?>

<div class = "table-reponsive">                   
                    <form method = "POST" class = "form-inline" action = "propertyInfoDelete.php">
                    <label class = "m-2" for="exampleFormControlInput1">Enter the address of the property you would like to delete</label>
                        <div class = "form-group">
                            <input type="hidden" id="deleteTupleRequest" name="deleteTupleRequest">
                            <input type="text" class="form-control m-3" id="address" name = "address" placeholder="address">
                            <input type="submit" class="btn btn-danger m-3" value="delete" name = "delete">
                        </div>      
                    </form>
                    
                    <table class = "table table-stripedbordered table-m">
                        <thead class ="thead-dark">
                            <tr>
                                <th>Address</th>
                                <th>Floors</th>
                                <th>Square Feet</th>
                                <th>Listed Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include('propertyInfoOperations.php'); ?>                      
                        </tbody>
                    </table>
                </div>
<?php include('propertyInfoBottomHalf.php'); ?>