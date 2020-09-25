package finch.robot.commands;

import finch.robot.Robot;
import finch.robot.framework.Command;
import finch.robot.input.KeyBinder;

import java.awt.*;

/**
 * Created by yosephsa on 9/29/2016.
 */
public class RelaySight extends Command {

    private static final int lightThresholdLeft = 30, lightThresholdRight = 18;
    private final Robot robot;

    public RelaySight() {
        this("Obstacle Evader");
    }

    public RelaySight(String name) {
        super(name);
        robot = Robot.getInstance();
        requires(Robot.getInstance().getLed());
    }

    @Override
    protected void init() {
        robot.getWindow().addText("Started " + getName());
    }

    @Override
    protected void update() {
        if (robot.getFinch().getLeftLightSensor() > lightThresholdLeft && robot.getFinch().getRightLightSensor() > lightThresholdRight)
            robot.getLed().setLED(Color.blue);
        else if (robot.getFinch().getLeftLightSensor() > lightThresholdLeft)
            robot.getLed().setLED(Color.green);
        else if (robot.getFinch().getRightLightSensor() > lightThresholdRight)
            robot.getLed().setLED(Color.red);
        else
            robot.getLed().setLED(Color.white);
    }

    @Override
    protected boolean isFinished() {
        return KeyBinder.getInstance().isBindingActive(KeyBinder.STOP_COMMAND);
    }

    @Override
    protected void end() {
        robot.getLed().setLED(Color.black);
        robot.getWindow().addText("Stopped " + getName());
    }

    @Override
    protected void interrupt() {
        robot.getLed().setLED(Color.black);
        robot.getWindow().addText("Interrupted " + getName());
    }
}
