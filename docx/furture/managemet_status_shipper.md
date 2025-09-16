Dưới đây là kế hoạch triển khai tính năng “Quản lý trạng thái đơn hàng” (admin cập nhật: xử lý/đóng gói/đang giao/đã giao/     
  cancel + xác nhận thanh toán):                                                                                                 
                                                                                                                                 
  Mục tiêu                                                                                                                       
                                                                                                                                 
  - Cho phép admin cập nhật trạng thái fulfillment (processing, packed, shipping, delivered, cancelled) và payment_status (paid) 
  theo quy tắc hợp lệ.                                                                                                           
  - Ghi nhận lịch sử thay đổi; đồng bộ với vận chuyển (shipment).                                                                
                                                                                                                                 
  Phạm vi                                                                                                                        
                                                                                                                                 
  - API Admin (đã có khung): PATCH /api/admin/orders/{id}                                                                        
  - Web Admin UI (đã có form ở trang show): xác nhận trạng thái/paid với kiểm tra quy tắc.                                       
  - Báo cáo: thống kê doanh thu dựa trên đơn delivered (đã dùng).                                                                
                                                                                                                                 
  Trạng thái & Quy tắc chuyển (Transition Rules)                                                                                 
                                                                                                                                 
  - Fulfillment:                                                                                                                 
      - pending → processing → packed → shipping → delivered                                                                     
      - Bất kỳ trước delivered → cancelled                                                                                       
      - delivered, cancelled: terminal (không chuyển tiếp)                                                                       
  - Payment:                                                                                                                     
      - COD: payment_status=paid chỉ khi đơn ở delivered (COD confirm)                                                           
  - Tích hợp shipment:                                                                                                           
      - Khi push-to-shipping: nếu pending → đổi processing; shipment.status requested → ready/picking/in_transit → delivered     
  (qua webhook).                                                                                                                 
  - Lỗi:                                                                                                                         
      - Chặn update nếu trạng thái không hợp lệ; trả 400 (API) và hiển thị lỗi (web).                                            
                                                                                                                                 
  Thiết kế dữ liệu (giữ nguyên)                                                                                                  
                                                                                                                                 
  - orders: status enum, payment_status enum                                                                                     
  - shipments: status enum, shipment_events: log sự kiện                                                                         
  - Optional: order_status_logs (nếu cần log lịch sử chi tiết)                                                                   
                                                                                                                                 
  Công việc cụ thể                                                                                                               
                                                                                                                                 
  1. API Admin                                                                                                                   
                                                                                                                                 
  - Củng cố endpoint PATCH /api/admin/orders/{id} (đã có):                                                                       
      - Kiểm tra canTransition(from,to)                                                                                          
      - Chỉ cho phép payment_status=paid nếu status current/next là delivered                                                    
      - Trả về order JSON sau cập nhật                                                                                           
                                                                                                                                 
  2. Web Admin UI                                                                                                                
                                                                                                                                 
  - Trang admin/orders/{id} (đã có form):                                                                                        
      - Sửa UI: hiển thị trạng thái hiện tại, dropdown trạng thái hợp lệ tiếp theo (disable trạng thái không hợp lệ)             
      - Nút “Xác nhận thanh toán COD” chỉ hiển thị khi status=delivered và payment_status!=paid                                  
      - Hiển thị lịch sử Shipment Events (đã có)                                                                                 
                                                                                                                                 
  3. Ghi log trạng thái (tùy chọn)                                                                                               
                                                                                                                                 
  - Thêm bảng order_status_logs (order_id, from_status, to_status, changed_by, at)                                               
  - Ghi log mỗi lần update thành công                                                                                            
                                                                                                                                 
  4. Đồng bộ vận chuyển                                                                                                          
                                                                                                                                 
  - Khi chuyển shipping → delivered:                                                                                             
      - Nếu có shipment: cập nhật shipment.status=delivered, tạo shipment_event delivered (nếu chưa có)                          
  - Khi cancel đơn:                                                                                                              
      - Xử lý release stock_reserved (cần checkout service mở rộng revert) — bổ sung nếu có yêu cầu                              
                                                                                                                                 
  5. Phân quyền                                                                                                                  
                                                                                                                                 
  - Admin API + Web: middleware ['auth:admin','admin'] đã có                                                                     
  - Policy: chỉ admin được update                                                                                                
                                                                                                                                 
  6. Kiểm thử                                                                                                                    
                                                                                                                                 
  - API tests:                                                                                                                   
      - pending→processing ok; processing→shipping (bị chặn, phải qua packed)                                                    
      - Đánh dấu paid khi delivered ok, trước delivered bị chặn                                                                  
  - UI tests: cập nhật trạng thái và xác nhận paid, hiển thị lỗi hợp lệ                                                          
                                                                                                                                 
  7. UX                                                                                                                          
                                                                                                                                 
  - Hiển thị badge màu cho trạng thái ở danh sách/chi tiết                                                                       
  - Toast/bảng thông báo khi cập nhật thành công                                                                                 
                                                                                                                                 
  Mở rộng (phase sau)                                                                                                            
                                                                                                                                 
  - Hủy đơn → hoàn stock_reserved (release)                                                                                      
  - Log trạng thái đầy đủ trong order_status_logs                                                                                
  - Webhook vận chuyển nâng cao (map state provider→internal state)                                                              
  - Thông báo (email/SMS) khi status đổi (dùng notification_templates/logs trong database.json)                                  
                                                                                                                                 
  Tiến độ đề xuất                                                                                                                
                                                                                                                                 
  - Ngày 1: Củng cố API + UI form + validate                                                                                     
  - Ngày 2: (tùy chọn) Thêm order_status_logs + UX badge                                                                         
  - Ngày 3: Tích hợp release stock khi cancel + kiểm thử                                                                         
                                                                                                                                 
