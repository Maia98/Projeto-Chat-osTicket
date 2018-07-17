<?php

    require_once(INCLUDE_DIR.'class.ticket.php');
    require_once(INCLUDE_DIR.'class.user.php');
    require_once(INCLUDE_DIR.'class.pdf.php');

    // Request
    $relatorio_selected = $_GET['relatorio_selected'];
    $data_ini = $_GET['data_ini'];
    $data_fim = $_GET['data_fim'];
    $departamento_selected = $_GET['departamento_selected'];
    $assunto_selected = $_GET['assunto_selected'];
    $status = $_GET['status'];


    // PDF
    $pdf      = new Pdf();
    $title    = null;
    $content  = null;
    $filename = null;

    $ticket = new Ticket();

    $content  = "<table class='table'>
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Data</th>
                        <th>Assunto</th>
                        <th>De</th>
                        <th>Prioridade</th>
                        <th>Atribuido a</th>
                        <th>Departamento</th>
                    </tr>
                </thead>";

    $content_qtd = "<table class='table'>
                     <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Assunto</th>
                            <th>Qtd</th>
                        </tr>
                    </thead>";

    $query = "SELECT ti.number AS ticket_number, ti.created AS ticket_data, lti.value AS ticket_assunto, us.name AS ticket_user, pri.priority_desc AS ticket_prioridade, sta.firstname AS ticket_atribuido_firstname, sta.lastname AS ticket_atribuido_lastname, de.dept_name as departamento, ti.closed as ticket_fechado  
                FROM ostau_ticket ti 
                LEFT JOIN ostau_department de ON ti.dept_id = de.dept_id 
                LEFT JOIN ostau_sla sla ON ti.sla_id = sla.id 
                LEFT JOIN ostau_staff sta ON ti.staff_id = sta.staff_id 
                LEFT JOIN ostau_user us ON ti.user_id = us.id 
                LEFT JOIN ostau_ticket_status tis ON ti.status_id = tis.id 
                LEFT JOIN ostau_ticket__cdata cdata ON ti.ticket_id = cdata.ticket_id 
                LEFT JOIN ostau_ticket_priority pri ON cdata.priority = pri.priority_id
			    LEFT JOIN ostau_list_items lti ON (lti.id = cdata.subject)
                LEFT JOIN ostau_ticket_event tev on (tev.ticket_id = ti.ticket_id)
                WHERE ti.number is not null ";


    switch ($relatorio_selected){
        case 'todos_tickets':

            $title = 'Todos os Tickets';
            $filename = 'todos_tickets';


            $query = $ticket->filtros_padrao($query, $data_ini, $data_fim, $departamento_selected, $assunto_selected, $status);


               //Agrupando ticket por seu ID 
              $query =  $query." GROUP BY ti.number";

            $results = db_query($query);
          

            while($row = db_fetch_array($results)){
             $content .= "<tbody>
                                <tr>
                                    <td>".$row["ticket_number"]."</td>
                                    <td>".date("d-m-Y H:m:s", strtotime($row["ticket_data"]))."</td>
                                    <td>".$row["ticket_assunto"]."</td>
                                    <td>".$row["ticket_user"]."</td>
                                    <td>".$row["ticket_prioridade"]."</td>
                                    <td>".$row["ticket_atribuido_firstname"]." ".$row["ticket_atribuido_lastname"]."</td>
                                    <td>".$row["departamento"]."</td>
                                </tr>
                            </tbody>";
            }

            $content .= "</table>";
            $pdf->setOrientation('L');
            break;

            case 'tickets_atrasado':
                $title = 'Tickets Atrasados';
                $filename = 'tickets_atrasado';




                $query .= " AND tev.state = 'overdue' ";
                
                $query = $ticket->filtros_padrao($query, $data_ini, $data_fim, $departamento_selected, $assunto_selected, $status);

                $query .= " GROUP BY ticket_number, ticket_data, ticket_assunto, ticket_user, ticket_prioridade, ticket_atribuido_firstname, ticket_atribuido_lastname, departamento, ticket_fechado";


                $results = db_query($query);
                while($row = db_fetch_array($results)){
                    $content .= "<tbody>
                                    <tr>
                                        <td>".$row["ticket_number"]."</td>
                                        <td>".date("d-m-Y H:m:s", strtotime($row["ticket_data"]))."</td>
                                        <td>".$row["ticket_assunto"]."</td>
                                        <td>".$row["ticket_user"]."</td>
                                        <td>".$row["ticket_prioridade"]."</td>
                                        <td>".$row["ticket_atribuido_firstname"]." ".$row["ticket_atribuido_lastname"]."</td>
                                        <td>".$row["departamento"]."</td>
                                    </tr>
                                </tbody>";
                }

            $content .= "</table>";
            $pdf->setOrientation('L');
            break;

            case 'tickets_atrasado_qtd':
                $title = 'Tickets Atrasados P/QTD';
                $filename = 'tickets_atrasado_qtd';

                $query = "SELECT de.dept_name as departamento, lti.value AS assunto, COUNT(ti.ticket_id) as qtd
                          FROM ostau_ticket ti
                          JOIN ostau_department de ON (de.dept_id = ti.dept_id)
                          JOIN ostau_ticket__cdata cdata on (cdata.ticket_id = ti.ticket_id)
                          JOIN ostau_list_items lti ON (lti.id = cdata.subject)
                          JOIN ostau_ticket_event tev on (tev.ticket_id = ti.ticket_id)
                          WHERE tev.state = 'overdue'";


                $query = $ticket->filtros_padrao($query, $data_ini, $data_fim, $departamento_selected, $assunto_selected, $status);

                $query .= " GROUP BY  departamento, assunto";

                $results = db_query($query);
                while($row = db_fetch_array($results)){
                    $content_qtd  .= "<tbody>
                                    <tr>
                                        <td>".$row["departamento"]."</td>
                                        <td>".$row["assunto"]."</td>
                                        <td>".$row["qtd"]."</td>
                                    </tr>
                                    
                                </tbody>";
                }

                //Total dos Relatórios Atrasados...
                $queryQtd = "SELECT COUNT(ti.ticket_id) AS qtd
                FROM ostau_ticket ti
                          JOIN ostau_department de ON (de.dept_id = ti.dept_id)
                          JOIN ostau_ticket__cdata cdata on (cdata.ticket_id = ti.ticket_id)
                          JOIN ostau_list_items lti ON (lti.id = cdata.subject)
                          JOIN ostau_ticket_event tev on (tev.ticket_id = ti.ticket_id)
                          WHERE tev.state = 'overdue'";

                $queryQtd = $ticket->filtros_padrao($queryQtd, $data_ini, $data_fim, $departamento_selected, $assunto_selected, $status);

                $resultTotal = db_query($queryQtd);
                $total = db_fetch_array($resultTotal);
                $total  = (int) $total['qtd'];

                $content_qtd .= "
                                <tr>
                                    <td colspan='2'>
                                     <strong>Total</strong>
                                    </td>
                                    <td><strong>".$total."</strong></td>
                                </tr>
                            </table>";

            $content = $content_qtd;
            break;

            case 'tickets_qtd':
                $title = 'Tickets P/QTD';
                $filename = 'tickets_qtd';

                $query = "SELECT de.dept_name as departamento, lti.value AS assunto, COUNT(ti.ticket_id) as qtd
                          FROM ostau_ticket ti
                          JOIN ostau_department de ON (de.dept_id = ti.dept_id)
                          JOIN ostau_ticket__cdata cdata on (cdata.ticket_id = ti.ticket_id)
                          JOIN ostau_list_items lti ON (lti.id = cdata.subject)
                          WHERE ti.number is not null";

                $query = $ticket->filtros_padrao($query, $data_ini, $data_fim, $departamento_selected, $assunto_selected, $status);

                $query .= " GROUP BY  departamento, assunto";
               
                $results = db_query($query);
                while($row = db_fetch_array($results)){
                    $content_qtd  .= "<tbody>
                                    <tr>
                                        <td>".$row["departamento"]."</td>
                                        <td>".$row["assunto"]."</td>
                                        <td>".$row["qtd"]."</td>
                                    </tr>
                                </tbody>";
                }


                //Total de Tickets...
                $queryQtd = "SELECT COUNT(ti.ticket_id) AS qtd
                          FROM ostau_ticket ti
                          JOIN ostau_department de ON (de.dept_id = ti.dept_id)
                          JOIN ostau_ticket__cdata cdata on (cdata.ticket_id = ti.ticket_id)
                          JOIN ostau_list_items lti ON (lti.id = cdata.subject)
                          WHERE ti.number is not null";

                $queryQtd = $ticket->filtros_padrao($queryQtd, $data_ini, $data_fim, $departamento_selected, $assunto_selected, $status);

                $resultTotal = db_query($queryQtd);
                $total = db_fetch_array($resultTotal);
                $total  = (int) $total['qtd'];

            $content_qtd .= "
                            <tr>
                                <td colspan='2'>
                                 <strong>Total</strong>
                                </td>
                                <td><strong>".$total."</strong></td>
                            </tr>
                        </table>";
            $content = $content_qtd;
            break;
    }

    $pdf->setTitle($title);
    $pdf->setNameFile($filename);
    $pdf->setContent($content);
    $pdf->createPdf();
    $pdf->openPdf();

?>