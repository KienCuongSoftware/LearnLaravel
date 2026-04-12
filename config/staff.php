<?php

return [

    /*
    | Đơn ở trạng thái cần xử lý mà không đổi trạng thái quá số giờ này → hiện cảnh báo trên dashboard staff.
    */
    'stale_order_hours' => (int) env('STAFF_STALE_ORDER_HOURS', 24),

    /*
    | Cổng SMS (Twilio, …) — bật khi đã cấu hình tích hợp thật.
    */
    'sms_enabled' => (bool) env('STAFF_SMS_ENABLED', false),

];
