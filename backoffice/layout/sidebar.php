<?php
// session_start();
$currentURL = $_SERVER['REQUEST_URI'];
$fileName = substr(strrchr($currentURL, '/'), 1);

$id = $_SESSION['staff_id'];
$sql = "select * from user where id='$id'";
$result = $connect->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];
?>
<div id="sidebar" class="sidebar">
    <div class="d-flex justify-content-between">
        <div class="sidebar-logo">
            <img src="asset/images/sidebar-logo.png" alt="logo-image">
        </div>
        <div class="menu-close-button align-items-center">
            <i class="bi bi-x" onclick="handleSidebar()"></i>
        </div>
    </div>
    <ul class="d-flex flex-column p-0 m-0 sidebar-menu">
        <?php if ($role == 1) { ?>
            <li>
                <a class="d-flex align-items-center <?php echo (strpos($fileName, 'index') !== false ? 'active' : '') ?>" href="./index">
                    <i class="bi bi-clipboard-data mx-2"></i>
                    <span class="" style="padding-top:6px;">Übersicht</span>
                </a>
            </li>
            <li>
                <a class="d-flex align-items-center <?php echo (strpos($fileName, 'doctors') !== false ? 'active' : '') ?>" href="doctors">
                    <i class="bi bi-person-lines-fill mx-2"></i>
                    <span class="" style="padding-top:3px;">Doktoren</span>
                </a>
            </li>
            <li>
                <a class="d-flex align-items-center <?php echo (strpos($fileName, 'nurse') !== false ? 'active' : '') ?>" href="nurse">
                    <i class="bi bi-people-fill mx-2"></i>
                    <span style="padding-top:3px;">Mitarbeiter</span>  
                </a>
            </li>
            <li>
                <a class="d-flex align-items-center <?php echo (strpos($fileName, 'patients') !== false ? 'active' : '') ?>" href="patients">
                    <i class="mx-2 d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-person-vcard" viewBox="0 0 16 16">
                            <path
                                d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                            <path
                                d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96c.026-.163.04-.33.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1.006 1.006 0 0 1 1 12z" />
                        </svg>
                    </i>
                    <span style="padding-top:2px;">Patienten</span>
                </a>
            </li>
            <li>
                <a class="d-flex align-items-center <?php echo (strpos($fileName, 'time_slot') !== false ? 'active' : '') ?>" href="time_slot">
                    <i class="bi bi-calendar-check mx-2"></i>
                    <span class="pt-1">Zeitfenster</span>  
                </a>
            </li>
            <li>
                <a class="d-flex align-items-center <?php echo (strpos($fileName, 'servicesdata') !== false ? 'active' : '') ?>" href="servicesdata">
                    <i class="bi bi-card-checklist mx-2"></i>
                    <span style="padding-top:2px;">Leistungen</span>  
                </a>
            </li>
        
            <li>
                <a class="d-flex align-items-center <?php echo ($fileName == 'profile' ? 'active' : '') ?>" href="profile">
                    <i class="bi bi-person-circle mx-2"></i>
                    <span style="padding-top:3px;">Profil</span>
                </a>
            </li>
        <?php } elseif ($role == 2) { ?>
                <li>
                    <a class="d-flex align-items-center <?php echo (strpos($fileName, 'index') !== false ? 'active' : '') ?>" href="./index">
                    <i class="bi bi-clipboard-data mx-2"></i>
                        <span>Übersicht</span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center <?php echo (strpos($fileName, 'patients') !== false ? 'active' : '') ?>" href="patients">
                        <i class="mx-2 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-person-vcard" viewBox="0 0 16 16">
                                <path
                                    d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                                <path
                                    d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96c.026-.163.04-.33.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1.006 1.006 0 0 1 1 12z" />
                            </svg>
                        </i>
                        <span>Patienten</span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center <?php echo (strpos($fileName, 'time') !== false ? 'active' : '') ?>" href="time">
                        <i class="bi bi-clock mx-2"></i>
                        <span>Zeit</span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center <?php echo (strpos($fileName, 'services') !== false ? 'active' : '') ?>" href="services">
                        <i class="bi bi-clipboard-data mx-2"></i>
                        <span>Leistungen</span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center <?php echo ($fileName == 'profile' ? 'active' : '') ?>" href="profile">
                        <i class="bi bi-person-circle mx-2"></i>
                        <span>Profil</span>
                    </a>
                </li>
        <?php } else { ?>
                <li>
                    <a class="d-flex align-items-center <?php echo (strpos($fileName, 'employees') !== false ? 'active' : '') ?>" href="employees">
                        <i class="bi bi-person-lines-fill mx-2"></i>
                        <span>Arzt</span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center <?php echo (strpos($fileName, 'patients') !== false ? 'active' : '') ?>" href="patients">
                        <i class="mx-2 d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-person-vcard" viewBox="0 0 16 16">
                                <path
                                    d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                                <path
                                    d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96c.026-.163.04-.33.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1.006 1.006 0 0 1 1 12z" />
                            </svg>
                        </i>
                        <span>Patienten</span>
                    </a>
                </li>
        <?php } ?>    
            <li class="nav-menu-item">
                <a class="d-flex align-items-center" onclick="handlePageMenu()">
                    <i class="bi bi-file-earmark-fill mx-2"></i>
                    <span>Seiten</span>
                </a>
                <ul id="nav-menu-item-list">
                    <li class="cursor-pointer">Startseite</li>
                    <li class="cursor-pointer">Über uns</li>
                    <li class="cursor-pointer">Leistungen </li>
                    <li class="cursor-pointer">Ernennung</li>
                    <li class="cursor-pointer">Kontakt</li>
                    <li class="cursor-pointer">Profil</li>
                </ul>
            </li>
        </ul>
</div>