package finch.robot.subsystems;

import finch.robot.Robot;
import finch.robot.RobotConstants;
import finch.robot.framework.Subsystem;

/**
 * Created by yosephsa on 9/14/2016.
 */
public class DriveTrain extends Subsystem {

    /**
     * Sets the velocity of the robot to the given value in Inches per Second.
     *
     * @param v The velocity in Inches per second.
     */
    public void setVelocity(double v) {
        setVelocity(v, v);
    }

    /**
     * Sets the wheel velocities in units of Inches per second.
     *
     * @param rv The right wheel's velocity in In/s.
     * @param lv The left wheel's velocity in In/s.
     */
    public void setVelocity(double rv, double lv) {
        // Keep velocity in max min velocity bounds
        rv = Math.min(Math.max(rv, -RobotConstants.maxVelocity), RobotConstants.maxVelocity);
        lv = Math.min(Math.max(lv, -RobotConstants.maxVelocity), RobotConstants.maxVelocity);
        // Convert from inches per second to values ranging 0-255
        rv = rv / RobotConstants.maxVelocity * 255;
        lv = lv / RobotConstants.maxVelocity * 255;
        // Set bot velocity.
        Robot.getInstance().getFinch().setWheelVelocities((int) rv, (int) lv);
    }

    @Override
    public void init() {
        setVelocity(0, 0);
    }

    @Override
    public void end() {
        setVelocity(0, 0);
    }
}
