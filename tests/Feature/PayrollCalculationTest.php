<?php

use App\Http\Controllers\PayrollController;
use App\Models\Attendance;

function invokePrivateMethod(object $object, string $method, array $arguments = [])
{
    $reflection = new ReflectionMethod($object, $method);
    $reflection->setAccessible(true);

    return $reflection->invokeArgs($object, $arguments);
}

test('statutory PAYE follows the TRA monthly bands', function () {
    $controller = new PayrollController();

    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [200000.0]))->toBe(0.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [270000.0]))->toBe(0.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [300000.0]))->toBe(2400.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [520000.0]))->toBe(20000.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [600000.0]))->toBe(36000.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [760000.0]))->toBe(68000.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [900000.0]))->toBe(103000.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [1000000.0]))->toBe(128000.0);
    expect(invokePrivateMethod($controller, 'calculatePayeFromConstants', [1200000.0]))->toBe(188000.0);
});

test('attendance metrics treat unpaid and half pay leave correctly', function () {
    $controller = new PayrollController();

    $records = collect([
        new Attendance(['status_code' => 'AL', 'overtime_hours' => 2, 'night_hours' => 1]),
        new Attendance(['status_code' => 'UL']),
        new Attendance(['status_code' => 'A']),
        new Attendance(['status_code' => 'SLH']),
        new Attendance(['status_code' => 'SLF', 'overtime_hours' => 60]),
        new Attendance(['status_code' => '9', 'rest_day_hours' => 8, 'ph_hours' => 8]),
    ]);

    $metrics = invokePrivateMethod($controller, 'summarizeAttendanceMetrics', [$records]);

    expect($metrics['unpaid_leave_days'])->toBe(2.5);
    expect($metrics['overtime_hours'])->toBe(50.0);
    expect($metrics['rest_day_hours'])->toBe(8.0);
    expect($metrics['public_holiday_hours'])->toBe(8.0);
    expect($metrics['night_hours'])->toBe(1.0);
});
