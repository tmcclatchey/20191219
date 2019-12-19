SELECT
    COUNT(`userId`) AS `count`
FROM
    `user`
WHERE
    (
        `roleId`=:roleId
    )
    AND
    (
        (`userLogin` LIKE CONCAT('%', :query, '%')) OR
        (`userName` LIKE CONCAT('%', :query, '%')) OR
        (`userEmail` LIKE CONCAT('%', :query, '%')) OR
        (`userMobile` LIKE CONCAT('%', :query, '%')) OR
    )