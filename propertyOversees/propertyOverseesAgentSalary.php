<?php include('propertyOverseesTopHalf.php'); ?>

<label for="exampleFormControlSelect1"> The average salary agents make as their rating increases, and who have a rating higher than</label>

<div class = "table-reponsive">
                   <form method = "POST" action="propertyOverseesAgentSalary.php">
                       <input type="hidden" id="ratingMore" name="ratingMore">
                           <div class = "form-row">
                               <div class = "col" >
                                   <td> <input class = "form-control" type="text" id="Rating" name="Rating"> </td>
                               </div>
                               <div class = "col" >
                                   <td> <input type="submit" class="btn btn-warning" value="Submit" name = "ratingMore"> </td>
                               </div>
                           </div>
                   </form>
                    <table class = "table table-stripedbordered table-m">

                      <tbody>
                             <thead class ="thead-dark">
                                                  <tr>
                                                      <th>Agent Rating</th>
                                                      <th>Agent Salary</th>
                                                  </tr>
                            </thead>
                            <tr>
                                <div class = "form-check">
                                </div>
                            </tr>
                            <?php include('propertyOverseesOperations.php'); ?>
                        </tbody>
                    </table>
                </div>
<?php include('propertyOverseesBottomHalf.php'); ?>