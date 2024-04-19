<?php
   session_start();
   if (!isset($_SESSION['staff_id'])) {
       header("Location: login");
   }
include('config/database.php');
include('./layout/header.php');
include('./layout/sidebar.php');
 
   
   $sql = "select * from services where deleted_at IS NULL ORDER BY created_at DESC";
   $result = $connect->query($sql);
   $servicesList = array();
   
   
   if ($result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $servicesList[] = $row;
       }
   }
   
   $itemsPerPage = 25;
   $totalItems = count($servicesList);
   $totalPages = ceil($totalItems / $itemsPerPage);
   $currentPage = isset($_GET['page']) ? max(1, min((int) $_GET['page'], $totalPages)) : 1;
   $startIndex = ($currentPage - 1) * $itemsPerPage;
   $endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);
   
   ?>
<!-- Main -->
<div id="main-content">
   <div class="p-2 w-100">
      <div class="d-flex justify-content-center align-items-center">
         <h1 class="page-heading">Leistungen</h1>
      </div>
      <div class="d-flex flex-wrap">
         <div class="flex-grow-1"></div>
         <button type="submit" class="cursor-pointer custom-secondary-button mt-2 mt-sm-0 m-2"
            data-bs-toggle="modal" data-bs-target="#add-services"><i class="bi bi-plus"
            style="color: white;width: 20px;height: 20px;"></i>Leistung</button>
      </div>
      <div class="px-2">
         <div class="mt-4 custom-table" id="Search-Options" onchange="handleSelect('Search-input')">
            <div class=" table-responsive">
               <table class="table pb-3">
                  <thead>
                     <tr>
                        <td style="width: 100px;">#</td>
                        <td class="text-center" style="min-width:300px;">DE</td>
                        <td class="text-center" style="min-width:300px;">EN</td>
                        <td style="max-width: 250px;">
                           <div class="text-center">Optionen</div>
                        </td>
                     </tr>
                  </thead>
                  <tbody>
                     <?php for ($i = $startIndex; $i <= $endIndex; $i++) {
                        ; ?>
                     <tr class="doctor-row">
                        <td>
                           <?php echo $i + 1; ?>
                        </td>
                        <td>
                           <?php echo $servicesList[$i]['services']; ?> 
                        </td>
                        <td>
                           <?php echo $servicesList[$i]['services_en']; ?> 
                        </td>
                        <td>
                        <div class="d-flex justify-content-center" style="min-width: 350px;">
                           <!-- Edit Button -->
                           <div class="editservices" id="editservices-<?php echo $i ?>" data-id="<?php echo $servicesList[$i]['id']; ?>" data-bs-toggle="modal" data-bs-target="#edit-services">
                              <i class="fas fa-edit cursor-pointer mx-3"></i>
                           </div>
                           
                           <!-- Delete Button -->
                           <div class="deleteservices text-danger" id="deleteservices-<?php echo $i ?>" data-id="<?php echo $servicesList[$i]['id']; ?>" data-bs-toggle="modal" data-bs-target="#deletedConfirmation">
                              <i class="fas fa-trash cursor-pointer mx-3"></i>
                           </div>
                        </div>

                        </td>
                     </tr>
                     <?php } ?>
                  </tbody>
               </table>
               <!-- pagination -->
               
                    <div class="white-table">
                    <ul class="custom-pagination" id="custom-pagination"></ul>
                    </div>
         </div>
         </div>
      </div>
   </div>
</div>

<!-- Modals -->
<!-- Add Services -->
<form id="AddServices" method="post">
   <div class="modal fade " id="add-services" data-bs-backdrop='static' tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
         <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
            <div class="d-flex justify-content-center align-items-center py-2">
               <div class="flex-grow-1"></div>
               <h1 class="modal-heading" style="font-weight: 800;">Leistung hinzufügen</h1>
               <div class="flex-grow-1"></div>
               <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;" aria-label="Close"></button>
            </div>
            <div class="row">
               <div class="col-lg-12 col-12">
                  <div class="form-group p-2 my-2">
                     <label class="my-1" for="Name">DE</label>
                     <input type="text" name="serviceGermany" class="form-control custom-input" id="serviceGermany" >
                     <span  class="error text-danger" id="serviceGermany-error"></span>
                  </div>
                  <div class="form-group p-2 my-2">
                     <label class="my-1" for="Name">EN</label>
                     <input type="text" name="servicesEnglish" class="form-control custom-input" id="servicesEnglish" >
                     <span  class="error text-danger" id="servicesEnglish-error"></span>
                  </div>
               </div>
            </div>
            <div class="d-flex justify-content-center align-items-center py-2 my-3">
               <button type="button" id="cancelSericesBtn" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Abbrechen</button>
               <button type="button" id="addServicesBtn" class="success-button cursor-pointer mx-1">Einreichen</button>
            </div>
         </div>
      </div>
   </div>
   <!-- Services Confirmation -->
   <div class="modal fade" id="Confirmation" tabindex="-1" data-bs-backdrop='static' role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
            <div class="d-flex justify-content-center align-items-center flex-column">
               <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
               <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
            </div>
            <div class="d-flex justify-content-center align-items-center">
               <button type="button" id="ConfirmationYesBtn" class="success-button cursor-pointer mx-1" data-bs-target="#show-info"
               >Ja</button>
               <button type="button" id="ConfirmationNoBtn" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Nein</button>
            </div>
         </div>
      </div>
   </div>
   <!-- Services Confirm Yes show info  -->
   <div class="modal fade " id="ShowInfo" tabindex="-1" data-bs-backdrop='static' role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered " role="document">
         <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
            <div class="d-flex justify-content-center align-items-center flex-column py-4">
               <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich eingefügt.</h1>
            </div>
            <div class="d-flex justify-content-center align-items-center">
               <button id="ServicesShowInfoBtn" name="ServicesShowInfoBtn" type="button" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
            </div>
         </div>
      </div>
   </div>
</form>

<!-- edit Staff -->
<form id="EditServices" method="post" action="">
   <div class="modal fade " id="edit-services" data-bs-backdrop='static' tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
         <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
            <div class="d-flex justify-content-center align-items-center py-2">
                <div class="flex-grow-1"></div>
                <h1 class="modal-heading" style="font-weight: 800;">Leistung bearbeiten</h1>
                <div class="flex-grow-1"></div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="width:50px;" aria-label="Close"></button>
            </div>
            <div class="row">
               <div class="col-lg-12 col-12">
                  <div class="form-group p-2 my-2">
                     <label class="my-1" for="Name">DE</label>
                     <input type="hidden" name="editId" id="editId">
                     <input type="text" name="editGermany" class="form-control custom-input" id="editGermany" >
                     <span  class="error text-danger" id="editGermany-error"></span>
                  </div>
                  <div class="form-group p-2 my-2">
                     <label class="my-1" for="Name">EN</label>
                     <input type="text" name="editEnglish" class="form-control custom-input" id="editEnglish" >
                     <span  class="error text-danger" id="editEnglish-error"></span>
                  </div>
               </div>
            </div>
            <div class="d-flex justify-content-center align-items-center py-2 my-3">
               <button type="button" id="cancelEdit" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Abbrechen</button>
               <button type="button" id="updateServicesBtn" class="success-button cursor-pointer mx-1">Update</button>
            </div>
         </div>
      </div>
   </div>
   <!-- staff Confirmation -->
   <div class="modal fade " id="EditSlotConfirmation" data-bs-backdrop='static' tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
            <div class="d-flex justify-content-center align-items-center flex-column">
               <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
               <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
            </div>
            <div class="d-flex justify-content-center align-items-center">
               <button type="button" class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Nein</button>
               <button type="button" id="EditConfirmationYesBtn" class="success-button cursor-pointer mx-1"
                  >Ja</button>
            </div>
         </div>
      </div>
   </div>
   <!-- staff show info  -->
   <div class="modal fade " id="editSlotShowInfo" data-bs-backdrop='static' tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered " role="document">
         <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
            <div class="d-flex justify-content-center align-items-center flex-column py-4">
               <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich aktualisiert.</h1>
            </div>
            <div class="d-flex justify-content-center align-items-center">
               <button id="StaffShowInfoBtn" type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
            </div>
         </div>
      </div>
   </div>
</form>

<!-- slot delete models  -->
<div class="modal fade" data-bs-backdrop='static' id="deletedConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
   aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
         <div class="d-flex justify-content-center align-items-center flex-column">
            <h1 class="modal-heading" style="font-weight: 800;">Sind Sie sicher?</h1>
            <p class="mb-h text-danger">Diese Aktion ist nicht rückgängig zu machen.</p>
         </div>
         <div class="d-flex justify-content-center align-items-center">
           
               <input type="hidden" name="deleteid" id="deleteid">
               <button type="button"  class="cancel-button cursor-pointer mx-1" data-bs-dismiss="modal">Nein</button>
               <button type="button" id="deleteConfirmationYesBtn" name="deleteConfirmationYesBtn" class="mx-1 success-button cursor-pointer"
                  >Ja</button>
            
         </div>
      </div>
   </div>
</div>
<!-- staff show info  -->
<div class="modal fade" data-bs-backdrop='static' id="deleteSlotShowInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
   aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered " role="document">
      <div class="modal-content p-3 custom-modal" style="border-radius: 16px;">
         <div class="d-flex justify-content-center align-items-center flex-column py-4">
            <h1 class="modal-heading" style="font-weight: 800;font-size: var(--md-heading);">Datensatz erfolgreich gelöscht.</h1>
         </div>
         <div class="d-flex justify-content-center align-items-center">
            <button id="ServicesShow" type="submit" class="success-button cursor-pointer" data-bs-dismiss="modal">Okay</button>
         </div>
      </div>
   </div>
</div>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
   integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
   crossorigin="anonymous"></script>
<script src="asset/js/index.js"></script>
<script src="asset/js/pagination.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="asset/js/script.js"></script>


<script>
   const params = new URLSearchParams(window.location.search);
   const currentPage = params.get('page');
   <?php if($totalItems > $itemsPerPage){ ?>
   CreatePagination({
       elementId: "custom-pagination",
       totalPage: <?php echo $totalPages; ?>,
       currentPage:currentPage ? Number(currentPage) : 1
   })
   <?php } ?> 
</script>

<script>
   $("#add-services").on("shown.bs.modal", function() {
    $(this).find('input').eq(0).focus();
  });
</script>
<!-- logout script  -->
<?php include('./layout/script.php')?>
</body>
</html>