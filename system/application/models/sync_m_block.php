<?

class sync_m_block extends Model 
{
    function sync_m_block()
    {
        parent::Model(); 
		//$this->load->database();
    }
	
	function getblockadem($company){
        $dsn = 'postgre://adempiere:adem5224878@192.168.1.4:5432/adempiere?port=5432&db_debug=TRUE';
		$this->load->database($dsn);
		
        $query = $this->db->query("SELECT a.block, a.blocktanam, a.value as tahuntanam,  a.plantedarea,  a.plantablearea,
    					COALESCE(a.unplantedarea,0) as unplantedarea,  a.plantedstatus,  a.realisasitanam,
    					a.jumlahpokok,  a.isactive,  a.inisial
						FROM (
        SELECT z.isactive, bk.c_salesregion_id, substring(bk.value from 5 for 5) as block,
            substring(bk.value from 5 for 5) || substring(cel.value  from 3 for 2) as blocktanam,
            bk.unplantedarea, z.realisasitanam, z.c_elementvalue_id, cel.value, z.c_period_id,
            bk.plantablearea, z.plantedarea, z.jumlahpokok, tnd.parent_id, bk.ad_orgtrx_id AS ad_org_id,
            CASE WHEN ad_org.value = 'PAI' THEN 'TPAI' 
            ELSE ad_org.value END AS inisial, ad_org.name as namapt,
            CASE z.plantedstatus
                WHEN '2'::bpchar
                THEN 'Tidak Tahu'::text
                WHEN '1'::bpchar
                THEN 'Tanaman Menghasilkan'::text
                WHEN '0'::bpchar
                THEN 'Tanaman Belum Menghasilkan'::text
                ELSE NULL::text
            END AS plantedstatus
        	FROM z_bloktanam z
			LEFT JOIN c_salesregion bk ON z.c_salesregion_id = bk.c_salesregion_id
			LEFT JOIN ad_org ON ad_org.ad_org_id = bk.ad_orgtrx_id
			LEFT JOIN ad_treenode tnd ON z.c_salesregion_id = tnd.node_id
			LEFT JOIN c_period per ON z.c_period_id = per.c_period_id
			LEFT JOIN ad_org org ON bk.ad_org_id = org.ad_org_id
			LEFT JOIN c_elementvalue cel ON cel.c_elementvalue_id = z.c_elementvalue_id
        	WHERE bk.issummary = 'N'::bpchar AND bk.isactive = 'Y'::bpchar
                AND  bk.plantablearea > (0)::NUMERIC AND tnd.ad_tree_id = (1000013)::NUMERIC
        	GROUP BY bk.c_salesregion_id, bk.value, cel.value, z.plantedarea, bk.unplantedarea,
            z.realisasitanam, z.plantedstatus, bk.plantablearea, z.ad_client_id, z.isactive,
            z.c_elementvalue_id, z.c_period_id, tnd.parent_id, bk.ad_orgtrx_id, ad_org.value,
            ad_org.name, z.jumlahpokok
    		) a
			WHERE a.c_period_id = ( SELECT MAX(z_bloktanam.c_period_id) AS MAX FROM z_bloktanam
                            		WHERE z_bloktanam.c_salesregion_id = a.c_salesregion_id )
					AND inisial = '".$company."'				 
			order by inisial ");
			
			$temp_result = array();
			foreach ( $query->result_array() as $row )
			{
				$temp_result [] = $row;
			}
			
			$this->db->close();
			return $temp_result;
    } 
}

?>