<?

class Model_m_fieldcrop extends Model 
{

    function Model_m_fieldcrop()
    {
        parent::Model(); 

		$this->load->database();
    }

	function info_m_fieldcrop ( $id )
	{

		$this->db->select( 'CONCESSIONID,COMPANY_CODE,NOHGU,USAGEID,FIELDCODE,ESTATECODE,DIVISIONCODE,DESCRIPTION,CROPTYPE,HECTPLANTED,PLANTINGDATE,PLANTINGDISTANCE,LASTSUPPHECT,LASTSUPPDATE,TOTSTANDOFFIELD,STANDPERHECT,CHECKROLLPRACTICE,PAYMENTMETHOD,HEIGHTCLASS,CROPPOLICY,YEARREPLANT,LONGCARRYPERC,SPECIES,HARVCOMMDATE,PALMSHARV,HECTHARV,HECTRESTED,CLONES,FIELDAGE,COSTCENTERID,INTIPLASMA,INACTIVE,INACTIVEDATE,TERAINTYPE,TOTALHECTARAGE,ROLLING,FLAT,LOWLAND,BLOCKID' );
		$this->db->where( 'CONCESSIONID', $id );
		$this->db->from('m_fieldcrop');

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return $row;
		}

	}
	
	function insert_m_fieldcrop ( $data )
	{
		$this->db->insert( 'm_fieldcrop', $data );
		
		return $this->db->insert_id();   
	}
	
	function update_m_fieldcrop ( $id, $data )
	{
		$this->db->where( 'CONCESSIONID', $id );  
		$this->db->update( 'm_fieldcrop', $data );   
	}
	
	function enroll_m_fieldcrop ( )
	{
		$this->db->select( 'CONCESSIONID,COMPANY_CODE,NOHGU,USAGEID,FIELDCODE,ESTATECODE,DIVISIONCODE,DESCRIPTION,CROPTYPE,HECTPLANTED,PLANTINGDATE,PLANTINGDISTANCE,LASTSUPPHECT,LASTSUPPDATE,TOTSTANDOFFIELD,STANDPERHECT,CHECKROLLPRACTICE,PAYMENTMETHOD,HEIGHTCLASS,CROPPOLICY,YEARREPLANT,LONGCARRYPERC,SPECIES,HARVCOMMDATE,PALMSHARV,HECTHARV,HECTRESTED,CLONES,FIELDAGE,COSTCENTERID,INTIPLASMA,INACTIVE,INACTIVEDATE,TERAINTYPE,TOTALHECTARAGE,ROLLING,FLAT,LOWLAND,BLOCKID');

		$this->db->from( 'm_fieldcrop' );

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result [] = $row;
		}

		return $temp_result;
	}
	
	//---------------------------------------untuk jqquery------------------------
	function read()
    {
        $this->db->select( 'CONCESSIONID,COMPANY_CODE,NOHGU,USAGEID,FIELDCODE,ESTATECODE,DIVISIONCODE,DESCRIPTION,CROPTYPE,HECTPLANTED,PLANTINGDATE,PLANTINGDISTANCE,LASTSUPPHECT,LASTSUPPDATE,TOTSTANDOFFIELD,STANDPERHECT,CHECKROLLPRACTICE,PAYMENTMETHOD,HEIGHTCLASS,CROPPOLICY,YEARREPLANT,LONGCARRYPERC,SPECIES,HARVCOMMDATE,PALMSHARV,HECTHARV,HECTRESTED,CLONES,FIELDAGE,COSTCENTERID,INTIPLASMA,INACTIVE,INACTIVEDATE,TERAINTYPE,TOTALHECTARAGE,ROLLING,FLAT,LOWLAND,BLOCKID');

		$this->db->from( 'm_fieldcrop' );

		$query = $this->db->get("m_fieldcrop");

        return $query->result();
    }
	
	function delete($id)
	{
		$this->db->where('FIELDCODE', $id);
		$this->db->delete('m_fieldcrop'); 
	}
	
	//TODO: check XSS and SQL injection here
    function readByPagination()
    {
        $limit = $this->input->post('rows');
        $page = $this->input->post('page');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        if(!$sidx) $sidx =1;
        $count = $this->db->count_all('m_fieldcrop');

        if( $count >0 ) {
            $total_pages = @(ceil($count/$limit));
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page=$total_pages;
        $start = $limit * $page - $limit;

        $this->db->limit($limit, $start);
        //$this->db->order_by("$sidx", "$sord");
        $objects = $this->db->get("m_fieldcrop")->result();
        $rows =  array();

        foreach($objects as $obj)
        {
	

            $cell = array();
			
			
							array_push($cell, $obj->CONCESSIONID);
							array_push($cell, $obj->COMPANY_CODE);
							array_push($cell, $obj->FIELDCODE);
							array_push($cell, $obj->ESTATECODE);
							array_push($cell, $obj->DIVISIONCODE);
							
							array_push($cell, $obj->NOHGU);
							array_push($cell, $obj->USAGEID);					
                            array_push($cell, $obj->BLOCKID);                         
                            array_push($cell, $obj->DESCRIPTION);
                            array_push($cell, $obj->CROPTYPE);
							
							array_push($cell, $obj->HECTPLANTED);
							array_push($cell, $obj->PLANTINGDATE);
							array_push($cell, $obj->PLANTINGDISTANCE);
							array_push($cell, $obj->LASTSUPPHECT);
							array_push($cell, $obj->LASTSUPPDATE);
					
							array_push($cell, $obj->TOTSTANDOFFIELD);
							array_push($cell, $obj->STANDPERHECT);
							array_push($cell, $obj->SPECIES);
							array_push($cell, $obj->HARVCOMMDATE);							
							array_push($cell, $obj->TOTALHECTARAGE);
							
							array_push($cell, $obj->CHECKROLLPRACTICE);
							array_push($cell, $obj->PAYMENTMETHOD);
							array_push($cell, $obj->CROPPOLICY);
							array_push($cell, $obj->YEARREPLANT);							
							array_push($cell, $obj->LONGCARRYPERC);
							
							array_push($cell, $obj->PALMSHARV);
							array_push($cell, $obj->HECTHARV);
							array_push($cell, $obj->HECTRESTED);
							array_push($cell, $obj->CLONES);							
							array_push($cell, $obj->FIELDAGE);
							
							array_push($cell, $obj->COSTCENTERID);
							array_push($cell, $obj->INTIPLASMA);
							array_push($cell, $obj->INACTIVE);
							array_push($cell, $obj->INACTIVEDATE);							
							array_push($cell, $obj->TERAINTYPE);
							
							array_push($cell, $obj->HEIGHTCLASS);
							array_push($cell, $obj->ROLLING);
							array_push($cell, $obj->FLAT);
							array_push($cell, $obj->LOWLAND);	
							
                        $row = new stdClass();
            $row->id = $cell[0];
            $row->cell = $cell;
            array_push($rows, $row);
        }

        $jsonObject = new stdClass();
        $jsonObject->page =  $page;
        $jsonObject->total = $total_pages;
        $jsonObject->records = $count;
        $jsonObject->rows = $rows;      

        return $jsonObject;
    }


}   

?>
