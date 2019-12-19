SELECT
    COUNT(`userId`) AS `count`
FROM
    `user`
WHERE
    (
        `managerId`=:managerId
    )
    AND
    (
        (`userLogin` LIKE CONCAT('%', :query, '%')) OR
        (`userName` LIKE CONCAT('%', :query, '%')) OR
        (`userEmail` LIKE CONCAT('%', :query, '%')) OR
        (`userMobile` LIKE CONCAT('%', :query, '%')) OR
    )