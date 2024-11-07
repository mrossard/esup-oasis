select dip.lib_dip, niv.niveau, dsi.lib_dsi
from version_etape vet
         join vdi_fractionner_vet vdv on vet.COD_ETP = vdv.COD_ETP and vet.COD_VRS_VET = vdv.COD_VRS_VET
         join version_diplome vdi on vdv.COD_DIP = vdi.COD_DIP and vdv.COD_VRS_VDI = vdi.COD_VRS_VDI
         join diplome dip on dip.cod_dip = vdi.cod_dip
         left outer join sec_dis_sis sds on sds.cod_sds = dip.cod_sds
         left outer join discipline_sis dsi on dsi.cod_dsi = sds.cod_dsi
         left outer join apogee.extern_niveau_etape niv on niv.cod_etp = vet.cod_etp
where vet.cod_etp = :codEtp
  and vet.cod_vrs_vet = :codVrsVet