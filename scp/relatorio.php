<?php
/*********************************************************************
    users.php

    Peter Rotich <peter@osticket.com>
    Jared Hancock <jared@osticket.com>
    Copyright (c)  2006-2014 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
// $title    = null;
// $content  = null;
// $filename = null;

// $relatorio_selected = $_GET['relatorio_selected'];
// $data_ini = $_GET['data_ini'];
// $data_fim = $_GET['data_fim'];
// $departamento_selected = $_GET['departamento_selected'];
// $assunto_selected = $_GET['assunto_selected'];

// $query = "SELECT ti.number AS ticket_number, ti.created AS ticket_data, cdata.subject AS ticket_assunto, us.name AS ticket_user, pri.priority_desc AS ticket_prioridade, sta.firstname AS ticket_atribuido_firstname, sta.lastname AS ticket_atribuido_lastname  
//     FROM ostau_ticket ti 
//     LEFT JOIN ostau_department de ON ti.dept_id = de.dept_id 
//     LEFT JOIN ostau_sla sla ON ti.sla_id = sla.id 
//     LEFT JOIN ostau_staff sta ON ti.staff_id = sta.staff_id 
//     LEFT JOIN ostau_user us ON ti.user_id = us.id 
//     LEFT JOIN ostau_ticket_status tis ON ti.status_id = tis.id 
//     LEFT JOIN ostau_ticket__cdata cdata ON ti.ticket_id = cdata.ticket_id 
//     LEFT JOIN ostau_ticket_priority pri ON cdata.priority = pri.priority_id";

// switch ($relatorio_selected){
//     case 'todos_tickets':
    
//         $title = 'Todos os Tickets';
//         $filename = 'todos_tickets';

//         if($data_ini && $data_fim){
//             $query .= " WHERE ti.created BETWEEN '".$data_ini." 00:00:00' AND '".$data_fim." 23:59:59'";
//         }

//         $results = db_query($query);
//         while($row = db_fetch_array($results)){
//          $content .= "<tbody>
//                             <tr>
//                                 <td>".$row["ticket_number"]."</td>
//                                 <td>".date("d-m-Y H:m:s", strtotime($row["ticket_data"]))."</td>
//                                 <td>".$row["ticket_assunto"]."</td>
//                                 <td>".$row["ticket_user"]."</td>
//                                 <td>".$row["ticket_prioridade"]."</td>
//                                 <td>".$row["ticket_atribuido_firstname"]." ".$row["ticket_atribuido_lastname"]."</td>
//                             </tr>
//                         </tbody>";
//         }

//         $content .= "</table>";
//         break;
// }




require('staff.inc.php');
require(STAFFINC_DIR.'relatorio-view-inc.php');

?>
