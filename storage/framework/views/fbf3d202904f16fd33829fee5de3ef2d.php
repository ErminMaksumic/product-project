<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            .center {
                text-align: center;
            }
            table .left {
                text-align: left;
            }
            table .right {
                text-align: right;
            }
            table .bold {
                font-weight: 600;
            }
            .bg-black {
                background-color: #000;
            }
            .f-white {
                color: #fff;
            }
            <?php $__currentLoopData = $styles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($style['selector']); ?> {
                <?php echo e($style['style']); ?>

            }
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </style>
    </head>
    <body>
        <?php
        $ctr = 1;
        $no = 1;
        $total = [];
        $grandTotalSkip = 1;
        $isOnSameGroup = true;
        $currentGroupByData = [];

        foreach ($showTotalColumns as $column => $type) {
            $total[$column] = 0;
        }

        if ($showTotalColumns != []) {
            foreach ($columns as $colName => $colData) {
                if (!array_key_exists($colName, $showTotalColumns)) {
                    $grandTotalSkip++;
                } else {
                    break;
                }
            }
        }

        $grandTotalSkip = !$showNumColumn ? $grandTotalSkip - 1 : $grandTotalSkip;
        ?>
        <table>
            <tr>
                <td colspan="<?php echo e(count($columns) + 1); ?>" class="center"><h1><?php echo e($headers['title']); ?></h1></td>
            </tr>
            <?php if($showMeta): ?>
                <?php $__currentLoopData = $headers['meta']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><b><?php echo e($name); ?></b></td>
                        <td colspan="<?php echo e(count($columns)); ?>"><?php echo e(ucwords($value)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </table>
        <table>
            <?php if($showHeader): ?>
            <thead>
                <tr>
                    <?php if($showNumColumn): ?>
                        <th class="left"><?php echo e(__('laravel-report-generator::messages.no')); ?></th>
                    <?php endif; ?>
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colName => $colData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(array_key_exists($colName, $editColumns)): ?>
                            <th class="<?php echo e(isset($editColumns[$colName]['class']) ? $editColumns[$colName]['class'] : 'left'); ?>"><?php echo e($colName); ?></th>
                        <?php else: ?>
                            <th class="left"><?php echo e($colName); ?></th>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <?php endif; ?>
            <tbody>
            <?php
            $__env = isset($__env) ? $__env : null;
            ?>
            <?php $__currentLoopData = $query->take($limit ?: null)->cursor(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    if ($groupByArr != []) {
                        $isOnSameGroup = true;
                        foreach ($groupByArr as $groupBy) {
                            if (is_object($columns[$groupBy]) && $columns[$groupBy] instanceof Closure) {
                                $thisGroupByData[$groupBy] = $columns[$groupBy]($result);
                            } else {
                                $thisGroupByData[$groupBy] = $result->{$columns[$groupBy]};
                            }

                            if (isset($currentGroupByData[$groupBy])) {
                                if ($thisGroupByData[$groupBy] != $currentGroupByData[$groupBy]) {
                                    $isOnSameGroup = false;
                                }
                            }

                            $currentGroupByData[$groupBy] = $thisGroupByData[$groupBy];
                        }

                        if ($isOnSameGroup === false) {
                            echo '<tr class="f-white">';
                            if ($showNumColumn || $grandTotalSkip > 1) {
                                echo '<td class="bg-black" colspan="' . $grandTotalSkip . '"><b>'.__('laravel-report-generator::messages.grand_total').'</b></td>';
                            }
                            $dataFound = false;
                            foreach ($columns as $colName => $colData) {
                                if (array_key_exists($colName, $showTotalColumns)) {
                                    if ($showTotalColumns[$colName] == 'point') {
                                        echo '<td class="right bg-black"><b>' . number_format($total[$colName], 2, '.', ',') . '</b></td>';
                                    } else {
                                        echo '<td class="right bg-black"><b>' . strtoupper($showTotalColumns[$colName]) . ' ' . number_format($total[$colName], 2, '.', ',') . '</b></td>';
                                    }
                                    $dataFound = true;
                                } else {
                                    if ($dataFound) {
                                        echo '<td class="bg-black"></td>';
                                    }
                                }
                            }
                            echo '</tr>';//<tr style="height: 10px;"><td colspan="99">&nbsp;</td></tr>';

                            // Reset No, Reset Grand Total
                            $no = 1;
                            foreach ($showTotalColumns as $showTotalColumn => $type) {
                                $total[$showTotalColumn] = 0;
                            }
                            $isOnSameGroup = true;
                        }
                    }
                ?>
                <tr align="center">
                    <?php if($showNumColumn): ?>
                        <td class="left"><?php echo e($no); ?></td>
                    <?php endif; ?>
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colName => $colData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $class = 'left';
                            // Check Edit Column to manipulate class & Data
                            if (is_object($colData) && $colData instanceof Closure) {
                                $generatedColData = $colData($result);
                            } else {
                                $generatedColData = $result->{$colData};
                            }
                            $displayedColValue = $generatedColData;
                            if (array_key_exists($colName, $editColumns)) {
                                if (isset($editColumns[$colName]['class'])) {
                                    $class = $editColumns[$colName]['class'];
                                }

                                if (isset($editColumns[$colName]['displayAs'])) {
                                    $displayAs = $editColumns[$colName]['displayAs'];
                                    if (is_object($displayAs) && $displayAs instanceof Closure) {
                                        $displayedColValue = $displayAs($result);
                                    } elseif (!(is_object($displayAs) && $displayAs instanceof Closure)) {
                                        $displayedColValue = $displayAs;
                                    }
                                }
                            }

                            if (array_key_exists($colName, $showTotalColumns)) {
                                $total[$colName] += $generatedColData;
                            }
                        ?>
                        <td class="<?php echo e($class); ?>"><?php echo e($displayedColValue); ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php $ctr++; $no++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($showTotalColumns != [] && $ctr > 1): ?>
                <tr class="f-white">
                    <?php if($showNumColumn || $grandTotalSkip > 1): ?>
                        <td colspan="<?php echo e($grandTotalSkip); ?>" class="bg-black"><b><?php echo e(__('laravel-report-generator::messages.grand_total')); ?></b></td> 
                    <?php endif; ?>
                    <?php $dataFound = false; ?>
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colName => $colData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(array_key_exists($colName, $showTotalColumns)): ?>
                            <?php $dataFound = true; ?>
                            <?php if($showTotalColumns[$colName] == 'point'): ?>
                                <td class="bg-black right"><b><?php echo e(number_format($total[$colName], 2, '.', ',')); ?></b></td>
                            <?php else: ?>
                                <td class="bg-black right"><b><?php echo e(strtoupper($showTotalColumns[$colName])); ?> <?php echo e(number_format($total[$colName], 2, '.', ',')); ?></b></td>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if($dataFound): ?>
                                <td class="bg-black"></td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </body>
</html>
<?php /**PATH C:\Users\User\OneDrive\Desktop\Project\PHP-project\product-project\resources\views/vendor/laravel-report-generator/general-excel-template.blade.php ENDPATH**/ ?>