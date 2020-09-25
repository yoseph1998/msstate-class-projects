package finch.robot.commands;

import finch.robot.Robot;
import finch.robot.framework.Command;

/**
 * Created by yosephsa on 9/14/2016.
 */
public class DriveDistance extends Command {

    private double velocity;
    private double time;
    private double distance;

    public DriveDistance(double d, double v) {
        this.velocity = v;
        this.distance = d;
        time = distance / velocity;
        setTimeout(time);
        requires(Robot.getInstance().getDriveTrain());
    }

    @Override
    protected void init() {
        Robot.getInstance().getWindow().addText("DD Initializing...");
        Robot.getInstance().getDriveTrain().setVelocity(velocity);
    }

    @Override
    protected void update() {
    }

    @Override
    protected boolean isFinished() {
        return isTimedOut();
    }

    @Override
    protected void end() {
        Robot.getInstance().getWindow().addText("DD Ending");
        Robot.getInstance().getDriveTrain().setVelocity(0);
    }

    @Override
    protected void interrupt() {
        Robot.getInstance().getDriveTrain().setVelocity(0);
    }
}
