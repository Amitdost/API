<?php 
class Email_model extends CI_Model 
{
    
        function __construct() {
            parent::__construct();
            $this->load->database();
        }

       public function send_mail( $data, $attachment="" ){

          $config['protocol'] = 'sendmail';
          $config['mailpath'] = '/usr/sbin/sendmail';
          $config['wordwrap'] = TRUE;
          $config['charset']    = 'utf-8';
          $config['newline']    = "\r\n";
          $config['mailtype'] = 'html'; // or html
          //$config['validation'] = TRUE; // bool whether to validate email or not   

          $this->email->set_header('MIME-Version', '1.0');
          $this->email->set_header('Content-type', 'text/html'); 
          $this->email->set_header('charset', 'UTF-8'); 

         

          $this->email->reply_to('anand.rai@appideasinc.com', 'Anand Rai');

          $this->email->initialize($config);
          $this->email->from( $data['from'], $data['name'], "anand.rai@appideasinc.com");
          $this->email->to($data['to']);
          //$this->email->cc($data['cc']);
          //$this->email->bcc('them@their-example.com');

          if( is_array($attachment) && count($attachment) ){
            foreach ($attachment as $i => $value) {
             
              $this->email->attach($attachment[$i]);
            }           
          }else{

            $this->email->attach($attachment);

          }

          $this->email->subject($data['subject']);
          $this->email->message($data['message']);

          $x =  $this->email->send();
          $this->email->clear(TRUE);  


          return $x;
       }

      public function getEmailTemplateListByCompanyId( $company_id ){

            $this->db->select( 'emailtemplate_id, template_name, slug');
            $this->db->from('email_template');
           if( $company_id == 0 ){

                $this->db->where('company_id', $company_id);
                
            }else{
            
                //$this->db->where('company_id', $company_id);
                $this->db->or_where("find_in_set(".$company_id.",assign_to_company) <> 0");
                $this->db->where('status ','1');  
            }
            $query = $this->db->get();
            return $result = $query->result_array();

       }

       public function getEmailTemplateBySlugAndCompanyId( $slug, $company_id ){

            $sql = "SELECT * FROM `email_template` where company_id = ".$company_id." And slug ='".$slug."' and company_id IN ( select assign_to_company from email_template where company_id = 0)";

            $result = $this->db->query( $sql )->row_array();
            return $result;

       }

}