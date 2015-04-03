SELECT 'Lo más nuevo' AS categoria, a.id_campana, a.subtitulo, MIN(b.precio_regular) AS precio_regular, MIN(b.precio_especial) AS precio_especial, SUM(IFNULL(b.vendidos,0)) AS vendidos
FROM cup_campana a
INNER JOIN cup_campana_opcion b ON a.id_campana = b.id_campana
GROUP BY a.id_campana, a.subtitulo



SELECT * 
FROM cup_campana a
WHERE a.id_campana = 1