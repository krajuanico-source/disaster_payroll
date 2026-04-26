<?php
session_start();
include "dbconnect.php";  // Database connection
error_reporting(0);
$emm=$_SESSION['userid'];
$payroll_no = $_POST['payroll_no'];

    // Fetch payroll details
    $sql = "SELECT * FROM tbl_payroll_list AS a 
			LEFT JOIN lib_sdo AS c ON sdo_id=a.sdo
			LEFT JOIN lib_fund_source AS d ON d.id=a.fund_source
			WHERE payroll_no = '$payroll_no'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $payroll = $result->fetch_assoc();
		$payroll_status = $payroll['payroll_status'];
		$sdo_id = $payroll['sdo_id'];
		
			
		if($payroll_status=='Closed'){ ?>
		<div class="container">
			  <div class="row">
			  <?php if($user_type=='Team Leader'){ ?>
				  <!-- Re-open Payroll Button on the left -->
				<div class="col-md-6 text-left">
				  <button type='button' class='btn btn-primary' id='openPayrollBtn'>Re-open Payroll</button>
				</div>
			  <?php }?>
				<!-- Generate Payroll Button and Page Dropdown on the right -->
				<div class="col-md-6 text-right">
					<button type='button' class='btn btn-success' id='generatePayrollBtn1'>Generate Payroll</button></center>
				</div>
			  </div>
			</div>
	<?php		
    echo "<center><br><br><table width='90%' style='text-align:left;'>
            <thead>
                <tr>
                    <th width='20%'>Payroll No</th>
                    <th width='20%'>Program</th>
                    <th width='30%'>Province</th>
                    <th width='20%'>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>".$payroll['payroll_no']."</td>
                    <td>".$payroll['program']."</td>
                    <td>".$payroll['province']."</td>
                    <td>".$payroll['date_created']."</td>
                </tr>
            </tbody>
          </table><br>
          <table width='90%' style='text-align:left;'>
            <thead>
                <tr>
                    <th width='20%'>Amount</th>
                    <th width='20%'>Barangay</th>
                    <th width='30%'>SDO</th>
                    <th width='20%'>Partner</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>".number_format($payroll['amount'],2)."</td>
                    <td>".$payroll['city_muni']."</td>
                    <td>".$payroll['sdo']."</td>
                    <td>".$payroll['fs_name']."</td>
                </tr>
            </tbody>
          </table><br>";

        
          $sql_bene = "SELECT * FROM ect_clean_list WHERE payroll_no='$payroll_no' AND STATUS IN ('Validated','Claimed') ORDER BY payroll_id ASC";
        $result_bene = $conn->query($sql_bene);
        
        if ($result_bene->num_rows > 0) {
            echo "<form id='updateStatusForm'>";
            echo "<table width='100%' id='bene_list' class='table table-bordered table-striped'>";
            echo "<thead><tr><th>No</th><th>First Name</th><th>Last Name</th><th>Date of Birth</th></tr></thead>";
            echo "<tbody>";
            while ($bene = $result_bene->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $bene['payroll_id'] . "</td>";
                echo "<td>" . $bene['first_name'] . "</td>";
                echo "<td>" . $bene['last_name'] . "</td>";
                echo "<td>" . $bene['birth_month'] . '-'. $bene['birth_day'] .'-'. $bene['birth_year'] ."</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</form>";
        } else {
            echo "<p>No beneficiaries found for this payroll.</p>";
        }
    } 
	else{ ?>
  <div class="row">

    <!-- Close Payroll Button on the left -->
    <div class="col-md-4 text-left">
      <button type='button' class='btn btn-danger' id='closePayrollBtn'>Close Payroll</button>
      <button type='button' class='btn btn-info' id='addDetails' onclick="confirmSaveDetails()">Add Details</button>
    </div>

    <!-- Generate Payroll Button and Page Dropdown on the right -->
    <div class="col-md-4 text-right">
      <div class="form-inline">
        <button type='button' class='btn btn-success mr-2' id='generatePayrollBtn'>Generate Payroll</button>
        <select class='status-dropdown form-control' id='pageNumberSelect'>
          <option value=''>Select Page</option>
          <?php
            $sql2 = "SELECT id FROM ect_clean_list WHERE payroll_no = '$payroll_no' AND status in ('Validated','Claimed') ORDER BY payroll_id ASC";
            $result2 = $conn->query($sql2);
            $totalBene = mysqli_num_rows($result2); // Get total number of beneficiaries
            $benePerPage = 10; // Number of beneficiaries per page
            $totalPages = ceil($totalBene / $benePerPage); // Calculate total pages              
            for ($i = $totalPages; $i >= 1; $i--) {
              echo "<option value='$i'>Page $i</option>";
            } 

          ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Dynamic Table for Payroll Details -->

    <center><br>
    <table width='50%' class='table table-striped'>
      <thead>
        <tr>
          <th width='10%'>Payroll No</th>
          <th width='15%'>Program</th>
          <th width='20%'>Title</th>
          <th width='10%'>Province</th>
          <th width='10%'>City/Municipality</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type='number' class='form-control' readonly value='<?php echo $payroll['payroll_no']; ?>' id='payroll_no'/></td>
          <td>
        <select class="form-control" name="program" id="programu">
          <option value="" disabled <?php echo (!$payroll['program']) ? 'selected' : ''; ?>>-- Select --</option>
          <?php
            $sqlp = "SELECT * FROM lib_program WHERE prog_status = 1";
            $resultp = mysqli_query($conn, $sqlp);
            while ($rowp = mysqli_fetch_assoc($resultp)) {
              $selected = ($payroll['program'] == $rowp['prog_name']) ? 'selected' : '';
              echo '<option value="' . $rowp['prog_name'] . '" ' . $selected . '>'
                  . $rowp['prog_name'] . '</option>';
            }
          ?>
        </select>
      </td>
          <td><input type='text' class='form-control' id='title' style='padding-right:20px' value='<?php echo $payroll['project_title']; ?>'/></td>
          <td>
		   <select class="form-control" name="province" id="province" style="padding-right:20px">
              <option value='<?php echo $payroll['province']; ?>'><?php echo $payroll['province']; ?></option>
              <option value='Davao City'>Davao City</option>
              <option value='Davao Del Sur'>Davao Del Sur</option>
              <option value='Davao Del Norte'>Davao Del Norte</option>
              <option value='Davao Oriental'>Davao Oriental</option>
              <option value='Davao De Oro'>Davao De Oro</option>
              <option value='Davao Occidental'>Davao Occidental</option>
              
            </select>
			</td>
      <td><input type='text' class='form-control' id='citymuni' style='padding-right:20px' value='<?php echo $payroll['city_muni']; ?>'/></td>
        </tr>
      </tbody>
    </table>

    <table width='50%' class='table table-striped' style='text-align:left;'>
      <thead>
        <tr>
          <th width='5%'>Date Created</th>
          <th width='5%'>Date From</th> 
          <th width='5%'>Date To</th> 
          <th width='5%'>Amount</th>
          <th width='15%'>SDO</th>
          <th width='15%'>Partner</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type='date' class='form-control' id='date' value='<?php echo $payroll['date_created']; ?>'/></td>
          <td>
          <input type='date' class='form-control' id='date_from'
            value='<?php echo $payroll['date_from'] ?? ""; ?>'>
        </td>

        <td>
          <input type='date' class='form-control' id='date_to'
            value='<?php echo $payroll['date_to'] ?? ""; ?>'>
        </td>
          <td><input type='number' class='form-control' id='amountu' value='<?php echo $payroll['amount']; ?>'/></td>
          <td>
            <input class="form-control" name="sdou" id="sdou" style="padding-right:20px" value='<?php echo $payroll['sdo']; ?>'></input>
          </td>
          <td>
            <select class="form-control" name="fund_sourceu" id="fund_sourceu">
              <option disabled selected value='<?php echo $payroll['fund_source']; ?>'><?php echo $payroll['fs_name']; ?></option>
              <?php
                $sql = "SELECT * FROM lib_fund_source WHERE fs_status = 'Active'";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                  echo '<option value="' . $row['id'] . '">' . $row['fs_name'] . '</option>';
                }
              ?>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    </center>
    
        <?php
	}
    } 

?><hr>
<div id="bene-list-container">
		  <!-- List of beneficiaries will be loaded here -->
		</div>
<script>
function confirmSaveDetails() {
  if (confirm("Are you sure you want to save the details?")) {
    saveDetails();
  }
}

function saveDetails() {
  var payroll_no = document.getElementById('payroll_no').value.trim();
  var title = document.getElementById('title').value.trim();
  var province = document.getElementById('province').value.trim();
  var date = document.getElementById('date').value.trim();
  var sdo = document.getElementById('sdou').value.trim();
  var amount = document.getElementById('amountu').value.trim();
  var fund_source = document.getElementById('fund_sourceu').value.trim();
  var citymuni = document.getElementById('citymuni').value.trim();
  var program     = document.getElementById('programu').value.trim();
  var date_from = document.getElementById('date_from').value.trim();
  var date_to   = document.getElementById('date_to').value.trim();

  // Check if any field is empty
  if (!payroll_no || !title || !province || !date || !sdo || !amount || !fund_source || !citymuni || !program) {
    alert("All fields are required. Please fill in all the details.");
    return; // Stop execution
  }

  $.ajax({
    type: 'POST',
    url: 'update_payroll_details.php',
    data: {
      payroll_no: payroll_no,
      title: title,
      province: province,
      date: date,
      amount: amount,
      sdo: sdo,
      citymuni:citymuni,
      fund_source: fund_source,
      program:     program,
      date_from: date_from, 
      date_to: date_to   
    },
    success: function(response) {
      alert("Details saved successfully!");
    },
    error: function() {
      alert("Error saving details. Please try again.");
    }
  });
}

</script>
<script>
  $(document).ready(function() {
    $('#pageNumberSelect').on('change', function() {
      var pageNumber = $(this).val();
      var benePerPage = 10;
      var payrollNo = '<?php echo $payroll_no ?>';
      var totalPages = '<?php echo $totalPages ?>';
      var isLastPage = (pageNumber == totalPages) ? 1 : 0;
      
      $.ajax({
        type: 'POST',
        url: 'load_bene_list.php',
        data: {pageNumber: pageNumber, benePerPage: benePerPage, payrollNo: payrollNo, isLastPage: isLastPage},
        success: function(response) {
          $('#bene-list-container').html(response);
        }
      });
    });
  });
</script>
<script>
$(document).ready(function() {
	$('#bene_list').DataTable({
        "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
        "iDisplayLength": 10
    });
	$('#bene_list1').DataTable({
        "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
        "iDisplayLength": 10
    });
    // Handle update status button click
    $('.update-status-btn').click(function() {
        var beneId = $(this).data('bene-id');
        var selectedStatus = $('select[data-bene-id="' + beneId + '"]').val();

        // AJAX request to update the status in the database
        $.ajax({
            url: 'update_status.php',
            type: 'POST',
            data: { bene_id: beneId, status: selectedStatus },
            success: function(response) {
                alert('Status updated successfully!');
            },
            error: function() {
                alert('Error updating status. Please try again.');
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    // Handle generate payroll button click
    $('#generatePayrollBtn1').click(function() {
        var payroll_no = '<?php echo $payroll_no; ?>';
//alert()
        // AJAX request to generate payroll (adjust the URL to your own logic for generating payroll)
        $.ajax({
            url: 'payroll_form_closed.php',
            type: 'POST',
            data: { payroll_no: payroll_no },
            success: function(response) {
				var url = 'payroll_form_closed.php?payroll_no=' + payroll_no;
				window.open(url, '_blank', 'target=_blank');
                // You can also trigger download or other actions here
				// Reload the page after a short delay
                setTimeout(function() {
                    //location.reload();
                }, 1000); // Reload the page after 2 seconds
            },
            error: function() {
                alert('Error generating payroll. Please try again.');
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    $('#generatePayrollBtn').click(function() {
        var payroll_no = '<?php echo $payroll_no; ?>';
        var selectedPage = $('#pageNumberSelect').val();

        if (!selectedPage) {
            alert("Please select a page before generating the payroll.");
            return; // Stop execution if no page is selected
        }

        var isLastPage = selectedPage == '<?php echo $totalPages ?>' ? 1 : 0;

        // AJAX request to generate payroll
        $.ajax({
            url: 'payroll_form.php',
            type: 'POST',
            data: { payroll_no: payroll_no, page_no: selectedPage, isLastPage: isLastPage },
            success: function(response) {
                var url = 'payroll_form.php?page_no=' + selectedPage + '&payroll_no=' + payroll_no + '&isLastPage=' + isLastPage;
                window.open(url, '_blank', 'target=_blank');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function() {
                alert('Error generating payroll. Please try again.');
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    // Handle close payroll button click
    $('#closePayrollBtn, #openPayrollBtn').click(function() {
        var payroll_no = '<?php echo $payroll_no; ?>';
        var action = $(this).attr('id') === 'closePayrollBtn' ? 'close' : 'reopen';
        
        $('#passwordModal').modal('show');
    });

    // Modal password verification
    $('#passwordModal').on('shown.bs.modal', function() {
        $('#passwordInput').focus();
    });

    $('#passwordModal').on('hidden.bs.modal', function() {
        $('#passwordInput').val('');
    });

    // Verify password button click event
    $('#verifyPasswordBtn').click(function() {
        var payroll_no = '<?php echo $payroll_no; ?>';
        var action = $('#closePayrollBtn').is(':visible') ? 'close' : 'reopen';
        var password = $('#passwordInput').val();
        
        // AJAX request to verify password
        $.ajax({
            url: 'verify_password.php',
            type: 'POST',
            data: { payroll_no: payroll_no, password: password },
            success: function(response) {
                if (response === 'true') {
                    // Password is correct, update payroll status
                    $.ajax({
                        url: action === 'close' ? 'update_payroll.php' : 'update_payroll_reopen.php',
                        type: 'POST',
                        data: { payroll_no: payroll_no },
                        success: function(response) {
							//alert(payroll_no);
                            $('#passwordModal').modal('hide');
                            $('#payrollModal').modal('hide');
							console.log(response); 
							location.reload(); // Reload the page
                        },
                        error: function() {
                            alert('Error updating status. Please try again.');
                        }
                    });
                } else {
                    // Password is incorrect
                    alert('Invalid password. Please try again.');
                }
            },
            error: function() {
                alert('Error verifying password. Please try again.');
            }
        });
    });
});
</script>
