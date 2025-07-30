<?php
session_start();
require_once 'includes/auth.php';

// Kullanıcı giriş kontrolü
checkLogin();

// Kullanıcı rolünü al
$userRole = getUserRole();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takvim - TalentBridge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Takvim özellikleri */
        .calendar-container {
            display: flex;
            gap: 20px;
        }
        
        .calendar-sidebar {
            width: 250px;
            flex-shrink: 0;
        }
        
        .calendar-main {
            flex-grow: 1;
        }
        
        /* Takvim görünümü */
        .calendar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f5f5f5;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .calendar-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .calendar-nav {
            display: flex;
            gap: 10px;
        }
        
        .calendar-nav-btn {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-bottom: 1px solid #e0e0e0;
        }
        
        .calendar-day-header {
            padding: 10px;
            text-align: center;
            font-weight: 600;
            border-right: 1px solid #e0e0e0;
            background: #f9f9f9;
        }
        
        .calendar-day-header:last-child {
            border-right: none;
        }
        
        .calendar-week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-bottom: 1px solid #e0e0e0;
        }
        
        .calendar-week:last-child {
            border-bottom: none;
        }
        
        .calendar-day {
            min-height: 100px;
            padding: 10px;
            border-right: 1px solid #e0e0e0;
            position: relative;
        }
        
        .calendar-day:last-child {
            border-right: none;
        }
        
        .day-number {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .today .day-number {
            background: #3498db;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .other-month {
            background: #f9f9f9;
            color: #aaa;
        }
        
        .event {
            margin-bottom: 5px;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .event-interview {
            background: #d4edda;
            color: #155724;
            border-left: 3px solid #28a745;
        }
        
        .event-meeting {
            background: #cce5ff;
            color: #004085;
            border-left: 3px solid #007bff;
        }
        
        .event-training {
            background: #fff3cd;
            color: #856404;
            border-left: 3px solid #ffc107;
        }
        
        .event-deadline {
            background: #f8d7da;
            color: #721c24;
            border-left: 3px solid #dc3545;
        }
        
        /* Etkinlik kategorileri */
        .event-categories {
            margin-bottom: 20px;
        }
        
        .event-category {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
        }
        
        .category-color {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            margin-right: 10px;
        }
        
        .interview-color {
            background: #28a745;
        }
        
        .meeting-color {
            background: #007bff;
        }
        
        .training-color {
            background: #ffc107;
        }
        
        .deadline-color {
            background: #dc3545;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar_main.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">Takvim</h1>
                    <div class="page-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Etkinlik
                        </button>
                    </div>
                </div>
                
                <div class="calendar-container">
                    <div class="calendar-sidebar">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Etkinlik Kategorileri</h3>
                            </div>
                            <div class="card-content">
                                <div class="event-categories">
                                    <div class="event-category">
                                        <span class="category-color interview-color"></span>
                                        <span class="category-name">Görüşmeler</span>
                                    </div>
                                    <div class="event-category">
                                        <span class="category-color meeting-color"></span>
                                        <span class="category-name">Toplantılar</span>
                                    </div>
                                    <div class="event-category">
                                        <span class="category-color training-color"></span>
                                        <span class="category-name">Eğitimler</span>
                                    </div>
                                    <div class="event-category">
                                        <span class="category-color deadline-color"></span>
                                        <span class="category-name">Son Tarihler</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Yaklaşan Etkinlikler</h3>
                            </div>
                            <div class="card-content">
                                <ul class="upcoming-events">
                                    <li class="upcoming-event">
                                        <div class="event-date">
                                            <span class="event-day">15</span>
                                            <span class="event-month">Haz</span>
                                        </div>
                                        <div class="event-details">
                                            <h4>Yazılım Ekibi Toplantısı</h4>
                                            <p><i class="fas fa-clock"></i> 14:00 - 15:30</p>
                                            <p><i class="fas fa-map-marker-alt"></i> Toplantı Salonu 2</p>
                                        </div>
                                    </li>
                                    <li class="upcoming-event">
                                        <div class="event-date">
                                            <span class="event-day">17</span>
                                            <span class="event-month">Haz</span>
                                        </div>
                                        <div class="event-details">
                                            <h4>Yeni Yazılım Geliştirici Görüşmesi</h4>
                                            <p><i class="fas fa-clock"></i> 10:00 - 11:00</p>
                                            <p><i class="fas fa-map-marker-alt"></i> Görüşme Odası 1</p>
                                        </div>
                                    </li>
                                    <li class="upcoming-event">
                                        <div class="event-date">
                                            <span class="event-day">20</span>
                                            <span class="event-month">Haz</span>
                                        </div>
                                        <div class="event-details">
                                            <h4>JavaScript Eğitimi</h4>
                                            <p><i class="fas fa-clock"></i> 09:00 - 12:00</p>
                                            <p><i class="fas fa-map-marker-alt"></i> Eğitim Salonu</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="calendar-main">
                        <div class="calendar">
                            <div class="calendar-header">
                                <div class="calendar-title">Haziran 2023</div>
                                <div class="calendar-nav">
                                    <button class="calendar-nav-btn">Bugün</button>
                                    <button class="calendar-nav-btn"><i class="fas fa-chevron-left"></i></button>
                                    <button class="calendar-nav-btn"><i class="fas fa-chevron-right"></i></button>
                                    <select class="calendar-view-selector">
                                        <option value="month">Ay</option>
                                        <option value="week">Hafta</option>
                                        <option value="day">Gün</option>
                                        <option value="list">Liste</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="calendar-grid">
                                <div class="calendar-day-header">Pzt</div>
                                <div class="calendar-day-header">Sal</div>
                                <div class="calendar-day-header">Çar</div>
                                <div class="calendar-day-header">Per</div>
                                <div class="calendar-day-header">Cum</div>
                                <div class="calendar-day-header">Cmt</div>
                                <div class="calendar-day-header">Paz</div>
                            </div>
                            
                            <div class="calendar-week">
                                <div class="calendar-day other-month">
                                    <div class="day-number">29</div>
                                </div>
                                <div class="calendar-day other-month">
                                    <div class="day-number">30</div>
                                </div>
                                <div class="calendar-day other-month">
                                    <div class="day-number">31</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">1</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">2</div>
                                    <div class="event event-meeting">Haftalık Toplantı</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">3</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">4</div>
                                </div>
                            </div>
                            
                            <div class="calendar-week">
                                <div class="calendar-day">
                                    <div class="day-number">5</div>
                                    <div class="event event-deadline">Proje Teslimi</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">6</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">7</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">8</div>
                                    <div class="event event-interview">İK Uzmanı Görüşmesi</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">9</div>
                                    <div class="event event-meeting">Haftalık Toplantı</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">10</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">11</div>
                                </div>
                            </div>
                            
                            <div class="calendar-week">
                                <div class="calendar-day">
                                    <div class="day-number">12</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">13</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">14</div>
                                    <div class="event event-training">React Eğitimi</div>
                                </div>
                                <div class="calendar-day today">
                                    <div class="day-number">15</div>
                                    <div class="event event-meeting">Yazılım Ekibi Toplantısı</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">16</div>
                                    <div class="event event-meeting">Haftalık Toplantı</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">17</div>
                                    <div class="event event-interview">Yazılım Geliştirici Görüşmesi</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">18</div>
                                </div>
                            </div>
                            
                            <div class="calendar-week">
                                <div class="calendar-day">
                                    <div class="day-number">19</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">20</div>
                                    <div class="event event-training">JavaScript Eğitimi</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">21</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">22</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">23</div>
                                    <div class="event event-meeting">Haftalık Toplantı</div>
                                    <div class="event event-deadline">Sprint Sonu</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">24</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">25</div>
                                </div>
                            </div>
                            
                            <div class="calendar-week">
                                <div class="calendar-day">
                                    <div class="day-number">26</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">27</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">28</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">29</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="day-number">30</div>
                                    <div class="event event-meeting">Haftalık Toplantı</div>
                                </div>
                                <div class="calendar-day other-month">
                                    <div class="day-number">1</div>
                                </div>
                                <div class="calendar-day other-month">
                                    <div class="day-number">2</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mobil menü toggle
            $('.menu-toggle').on('click', function() {
                $('.sidebar').toggleClass('active');
            });
        });
    </script>
</body>
</html>
