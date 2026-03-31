-- ============================================================
-- GPLM School - Class 2nd Blank Remarks Update
-- Table: result_student_health_records
-- Join:  users.admission_number -> result_student_health_records.student_id
-- Result Set ID: 3613
-- Total students: 31
-- Generated: 2026-03-25
-- ============================================================
-- BACKUP FIRST:
--   mysqldump -u root -p gplm_school result_student_health_records > backup_remarks.sql
--
-- RUN:
--   mysql -u root -p gplm_school < update_remarks_final.sql
-- ============================================================

-- Safety: only update rows where remark IS NULL or empty
-- Remove the "AND (r.remark IS NULL OR r.remark = '')" condition
-- if you want to forcefully overwrite existing remarks too.

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Muskan Kumari shows good progress in Maths and GK with growing confidence. Consistent practice in Hindi and English will further strengthen overall academic performance.'
WHERE u.admission_number = '2474'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Naina performs well in Maths and EVS with solid consistent marks. Focused effort in English will help achieve even greater academic excellence this year.'
WHERE u.admission_number = '2404'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Neel Vishwas demonstrates good strength in Maths with decent GK scores. Regular practice in Hindi and English will build a stronger academic foundation ahead.'
WHERE u.admission_number = '2414'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Nikita Arya shows commendable performance in Maths with good EVS marks. With dedicated practice in Hindi and English, overall academic results will improve further.'
WHERE u.admission_number = '2140'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Pallavi performs well in EVS and GK with consistent marks throughout the year. Continued practice in Hindi will help achieve stronger overall academic performance.'
WHERE u.admission_number = '2631'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Payal shows good progress in Maths with improvement from Term I to Term II. Consistent practice across all subjects will lead to even better academic achievement.'
WHERE u.admission_number = '2415'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Pihu demonstrates excellent performance in Maths and Hindi with outstanding dedication. Continued focused effort across all subjects will sustain this impressive academic excellence.'
WHERE u.admission_number = '2418'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Prashant Kushwaha performs well in Hindi and Maths with good consistent marks. With focused practice in English and EVS, overall academic performance will improve significantly.'
WHERE u.admission_number = '2781'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Priya shows commendable strength in Maths and EVS with improving performance. Consistent practice in Hindi and English will help achieve greater academic excellence overall.'
WHERE u.admission_number = '2413'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Priyanshi demonstrates excellent academic performance in EVS and GK with outstanding dedication. Continued consistent effort across all subjects will sustain this strong academic achievement.'
WHERE u.admission_number = '2407'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Rajan Kumar shows outstanding strength in Maths with good consistent performance. Focused effort in Hindi and English will further enhance overall academic excellence and results.'
WHERE u.admission_number = '2471'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Riddhiman Singh shows improvement in Hindi from Term I to Term II with good effort. Regular dedicated practice in all subjects will strengthen the overall academic foundation.'
WHERE u.admission_number = '2479'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Rishika Kumari Sharma shows remarkable improvement in Maths and Hindi in Term II. Continued dedicated effort across all subjects will lead to stronger academic performance.'
WHERE u.admission_number = '2477'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Ronak Meena demonstrates good strength in Maths and EVS with steady performance. Focused and regular practice in Hindi and English will improve overall academic achievement.'
WHERE u.admission_number = '2625'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Ruhi performs well in EVS and English with good consistent marks throughout. With dedicated practice in Hindi and Maths, overall academic performance will improve further.'
WHERE u.admission_number = '2626'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Sachi shows good performance in English and EVS with solid consistent marks. Focused practice in Maths and GK will help achieve even stronger overall academic results.'
WHERE u.admission_number = '2393'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Samar demonstrates steady performance across all subjects with good consistent effort. Regular focused practice in Hindi and Maths will lead to even better academic achievement.'
WHERE u.admission_number = '2381'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Sandhya excels in Maths and Hindi with excellent dedicated performance throughout the year. Continued consistent effort in English will help achieve even greater academic excellence.'
WHERE u.admission_number = '2405'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Saurav demonstrates outstanding performance across all subjects with excellent marks and dedication. Continued consistent effort will help sustain and further enhance this impressive academic excellence.'
WHERE u.admission_number = '2662'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Shivansh shows good improvement in Hindi and Maths from Term I to Term II. With consistent focused practice in English and EVS, overall academic performance will strengthen further.'
WHERE u.admission_number = '2420'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Siddharth Chawariya shows decent performance in EVS and English with steady effort. Regular practice in Maths and Hindi will help build a stronger academic foundation ahead.'
WHERE u.admission_number = '2470'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Simran shows good improvement in Maths with solid consistent performance in GK. Focused practice in Hindi and English will further enhance overall academic achievement significantly.'
WHERE u.admission_number = '2409'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Sonam shows encouraging improvement in EVS and Maths from Term I to Term II. Consistent dedicated practice in Hindi and English will strengthen overall academic performance further.'
WHERE u.admission_number = '2410'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Sushant demonstrates steady effort across all subjects with consistent performance throughout. Regular focused practice in all subjects will help improve overall academic achievement significantly.'
WHERE u.admission_number = '2628'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Tushar Kumar excels brilliantly across all subjects with outstanding marks and exceptional dedication. Continued consistent effort will help sustain this remarkable academic excellence throughout the year.'
WHERE u.admission_number = '2378'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Vaishnavi shows consistent performance across subjects with good effort throughout the year. Regular focused practice in Maths and Hindi will lead to stronger overall academic results.'
WHERE u.admission_number = '2388'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Vaniya shows improvement in Maths from Term I to Term II with encouraging progress. Consistent practice in Hindi and English will help build a stronger academic foundation.'
WHERE u.admission_number = '2627'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Vansh Rawat demonstrates strong performance across all subjects with excellent consistent marks. Continued dedicated effort will help maintain and further enhance this commendable academic excellence.'
WHERE u.admission_number = '2392'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Virat shows improvement in Maths in Term II with decent overall performance. Regular focused practice in Hindi and English will help achieve stronger academic results ahead.'
WHERE u.admission_number = '2416'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Yogesh shows excellent improvement in Maths and Hindi from Term I to Term II. Continued dedicated practice across all subjects will lead to even better academic achievement.'
WHERE u.admission_number = '2472'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

UPDATE result_student_health_records r
JOIN users u ON r.student_id = u.id
SET r.remark = 'Yug Arya shows good strength in GK with decent performance in Hindi. Regular consistent practice in Maths and EVS will help build a stronger overall academic foundation.'
WHERE u.admission_number = '2387'
  AND r.result_id = 3613
  AND (r.remark IS NULL OR r.remark = '');

-- ============================================================
-- VERIFY after running (should return 0 rows if all updated):
-- ============================================================
-- SELECT u.admission_number, u.name, r.remark
-- FROM result_student_health_records r
-- JOIN users u ON r.student_id = u.id
-- WHERE r.result_id = 3613
--   AND u.admission_number IN (
--     '2474','2404','2414','2140','2631','2415','2418','2781',
--     '2413','2407','2471','2479','2477','2625','2626','2393',
--     '2381','2405','2662','2420','2470','2409','2410','2628',
--     '2378','2388','2627','2392','2416','2472','2387'
--   )
-- ORDER BY u.admission_number;
