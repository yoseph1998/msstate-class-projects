package finch.robot.commands;

import finch.robot.Robot;
import finch.robot.RobotConstants;
import finch.robot.framework.Command;
import finch.robot.input.KeyBinder;

import java.awt.*;
import java.util.concurrent.TimeUnit;

/**
 * Created by yosephsa on 10/2/2016.
 */
public class ObstacleEvader extends Command {

    private final int LEFT = 0, RIGHT = 1;
    private int obstacle = -1;

    private long time0 = TimeUnit.SECONDS.toNanos(1);

    public ObstacleEvader() {
        this("Obstacle Evader");
    }

    public ObstacleEvader(String name) {
        super(name);
        requires(Robot.getInstance().getLed());
        requires(Robot.getInstance().getDriveTrain());
    }

    @Override
    protected void init() {
        Robot.getInstance().getWindow().addText(getName() + " Started");
    }

    @Override
    protected void update() {
        if (Robot.getInstance().getFinch().getObstacleSensors()[LEFT] && obstacle != LEFT) {
            Robot.getInstance().getDriveTrain().setVelocity(0);
            Robot.getInstance().getWindow().addText("    Left Obstacle");
            Robot.getInstance().getLed().setLED(Color.GREEN);
            obstacle = LEFT;
            time0 = System.nanoTime();
        } else if (Robot.getInstance().getFinch().getObstacleSensors()[RIGHT] && obstacle != RIGHT) {
            Robot.getInstance().getDriveTrain().setVelocity(0);
            Robot.getInstance().getWindow().addText("    Right Obstacle");
            Robot.getInstance().getLed().setLED(Color.RED);
            obstacle = RIGHT;
            time0 = System.nanoTime();
        }

        if ((int) TimeUnit.NANOSECONDS.toSeconds(System.nanoTime() - time0) == 1 && obstacle != -1) {
            if (obstacle == LEFT)
                Robot.getInstance().getDriveTrain().setVelocity(RobotConstants.maxVelocity, 0);
            else
                Robot.getInstance().getDriveTrain().setVelocity(0, RobotConstants.maxVelocity);
            time0 += TimeUnit.SECONDS.toNanos(1);
        }
    }

    @Override
    protected boolean isFinished() {
        return KeyBinder.getInstance().isBindingActive(KeyBinder.STOP_COMMAND);
    }

    @Override
    protected void end() {
        Robot.getInstance().getDriveTrain().setVelocity(0);
        Robot.getInstance().getLed().setLED(Color.BLACK);
        Robot.getInstance().getWindow().addText(getName() + " Stopped");
    }

    @Override
    protected void interrupt() {
        Robot.getInstance().getDriveTrain().setVelocity(0);
        Robot.getInstance().getLed().setLED(Color.BLACK);
        Robot.getInstance().getWindow().addText(getName() + " Interrupted");
    }
}
