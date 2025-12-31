 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <title>Quiz Management System</title>
     <style>
         body {
             background-color: #f4f6f8;
             font-family: Arial, Helvetica, sans-serif;
             padding: 20px;
         }

         .container {
             max-width: 600px;
             background: #ffffff;
             margin: auto;
             border-radius: 6px;
             overflow: hidden;
             box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
         }

         .header {
             background: #1976d2;
             color: #ffffff;
             padding: 16px;
             text-align: center;
         }

         .content {
             padding: 20px;
             color: #333;
         }

         .content p {
             margin: 8px 0;
             line-height: 1.6;
         }

         .label {
             font-weight: bold;
         }

         .message-box {
             background: #f1f1f1;
             padding: 12px;
             border-radius: 4px;
             margin-top: 8px;
         }

         .footer {
             text-align: center;
             font-size: 12px;
             color: #777;
             padding: 12px;
             border-top: 1px solid #eee;
         }
     </style>
 </head>

 <body>
     <div class="container">
         <div class="header">
             <h1>Quiz Management System</h1>
         </div>


             <p class="message-box">
             <div> Message:
                 {{ $data[ 'message']}}
             </div>
             </p>
         </div>

         <div class="footer">
             © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
         </div>

     </div>
 </body>

 </html>